<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;
use Carbon\Carbon;
use App\Models\Credito;
use App\Models\Movimiento;
use App\Models\Inventario;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Producto;
use App\Models\Facturacion;
use App\Models\Proveedor;
use App\Models\Mecanico;
use App\Models\DetalleFactura;

class FacturacionController extends Controller
{
    public function index()
    {
        $clientes    = Cliente::all();
        $proveedores = Proveedor::all();
        $productos   = Producto::where('estado', 1)->get();
        $inventarios = Inventario::with('producto')->where('cantidad_stock', '>', 0)->get();
        $mechanics   = Mecanico::orderBy('nombre')->get();
        $nextId      = (Facturacion::max('id_factura') ?? 0) + 1;

        return view('factura', compact(
            'clientes',
            'proveedores',
            'productos',
            'inventarios',
            'mechanics',
            'nextId'
        ));
    }

public function store(Request $request)
{
    $data = $request->validate([
        'id_cliente'         => 'required|exists:clientes,id_cliente',
        'id_producto'        => 'required|array|min:1',
        'id_producto.*'      => 'required|exists:productos,id_producto',
        'cantidad'           => 'required|array|min:1',
        'cantidad.*'         => 'required|integer|min:1',
        'mano_obra_general'  => 'required|numeric|min:0',
        'descuento'          => 'nullable|numeric|min:0',
        'monto_pago'         => 'required|numeric|min:0',
        'metodo_pago'        => 'required|in:efectivo,tarjeta,credito',
        'mecanico_id'        => 'nullable|exists:mecanicos,id_mecanico',
    ]);

    $descuento  = $data['descuento'] ?? 0;
    $manoObra   = $data['mano_obra_general'];
    $totalProd  = 0;

    foreach ($data['id_producto'] as $i => $pid) {
        $prod = Producto::findOrFail($pid);
        $totalProd += $prod->precio_venta * $data['cantidad'][$i];
    }

    $totalNeto = $totalProd + $manoObra - $descuento;

    if ($data['metodo_pago'] !== 'credito' && $data['monto_pago'] < $totalNeto) {
        return back()
            ->withErrors(['monto_pago' => 'El pago debe cubrir el total de la factura.'])
            ->withInput();
    }

    $vuelto = max(0, $data['monto_pago'] - $totalNeto);

    $factura = DB::transaction(function () use ($data, $totalNeto, $manoObra, $descuento, $vuelto) {
        $fac = Facturacion::create([
            'id_cliente'       => $data['id_cliente'],
            'id_usuario'       => auth()->id(),
            'total'            => $totalNeto,
            'mano_obra'        => $manoObra,
            'descuento'        => $descuento,
            'monto_pago'       => $data['monto_pago'],
            'vuelto'           => $vuelto,
            'metodo_pago'      => $data['metodo_pago'],
            'saldo_pendiente'  => $data['metodo_pago'] === 'credito' ? $totalNeto : 0,
            'mecanico_id'      => $data['mecanico_id'] ?? null,
        ]);

        foreach ($data['id_producto'] as $i => $pid) {
            $cantidad = $data['cantidad'][$i];

            // 1. Crear detalle de factura
            $fac->detalles()->create([
                'id_producto' => $pid,
                'cantidad'    => $cantidad,
            ]);

            // 2. Descontar del inventario (si no es crédito)
            if ($data['metodo_pago'] !== 'credito') {
                $inventario = Inventario::where('id_producto', $pid)->firstOrFail();
                $inventario->cantidad_stock = max(0, $inventario->cantidad_stock - $cantidad);
                $inventario->fecha_actualizacion = now();
                $inventario->save();

                // 3. Registrar movimiento
                Movimiento::create([
                    'id_producto' => $pid,
                    'tipo'        => 'salida',
                    'cantidad'    => $cantidad,
                    'descripcion' => 'Factura #' . $fac->id_factura,
                ]);
            }
        }

        if ($data['metodo_pago'] === 'credito') {
            Credito::create([
                'id_factura'   => $fac->id_factura,
                'monto_total'  => $totalNeto,
                'monto_pagado' => 0,
                'estado'       => Credito::EST_PENDIENTE,
            ]);
        }

        return $fac;
    });

    return redirect()
        ->route('factura')
        ->with('success', 'Factura creada correctamente.')
        ->with('invoice_id', $factura->id_factura);
}


 public function print($id)
    {
        $factura = Facturacion::with(['cliente', 'detalles.producto', 'usuario', 'credito'])
                              ->findOrFail($id);

        $pdf = PDF::loadView('factura_print', compact('factura'))
                  ->setPaper('a4', 'portrait');

        $filename = "factura_{$factura->id_factura}.pdf";

        return $pdf->download($filename);
    }


public function reprint($id)
{

    $factura = Facturacion::with(['cliente','usuario','detalles.producto'])
                         ->findOrFail($id);


    $pdf = app('dompdf.wrapper')->loadView('pdf.reprint', compact('factura'));

    return $pdf->stream("factura_{$id}.pdf");
}

public function audit(Request $request)
{
    // 1) Calculamos ventana: hoy 6am hasta mañana 6am
    $hoy6am     = now()->startOfDay()->addHours(6);
    $maniana6am = (clone $hoy6am)->addDay();

    // 2) Obtenemos detalle_factura con sus relaciones facturas→cliente y producto
    $movimientos = DetalleFactura::with([
            'producto',
            'factura.cliente'   // <-- aquí incluimos cliente
        ])
        ->whereHas('factura', function($q) use ($hoy6am, $maniana6am) {
            $q->whereBetween('fecha', [$hoy6am, $maniana6am]);
        })
        ->get();

    // 3) Generamos el PDF
    $pdf = app('dompdf.wrapper')
        ->loadView('pdf.audit', compact('movimientos','hoy6am','maniana6am'));

    // 4) Devolvemos para descarga
    return $pdf->download("auditoria_{$hoy6am->format('Ymd_Hi')}.pdf");
}

   public function rapida(Request $request)
    {
        $data = $request->validate([
          'id_producto' => 'required|exists:productos,id_producto',
          'id_cliente'  => 'required|exists:clientes,id_cliente',
          'cantidad'    => 'required|integer|min:1',
        ]);

        $producto = Producto::findOrFail($data['id_producto']);
        $total    = $producto->precio_venta * $data['cantidad'];

        $factura = Facturacion::create([
          'id_cliente'     => $data['id_cliente'],
          'id_usuario'     => auth()->id(),
          'total'          => $total,
          'monto_pago'     => 0,
          'vuelto'         => 0,
          'mano_obra'      => 0,
          'metodo_pago'    => 'credito',
          'saldo_pendiente'=> $total,
        ]);
        Credito::create([
          'id_factura'  => $factura->id_factura,
          'monto_total' => $total,
        ]);

        return redirect()->route('creditos.index')
                         ->with('success', "Factura rápida #{$factura->id_factura} creada a crédito.");
    }
public function pagoMecanico(Request $request)
{
    $start     = Carbon::parse($request->input('start', now()->startOfWeek()));
    $end       = Carbon::parse($request->input('end',   now()->endOfWeek()));
    $mechanics = Mecanico::orderBy('nombre')->get();
    $mecId     = $request->input('mecanico_id');

    $raw = Facturacion::selectRaw('DATE(fecha) as date, SUM(mano_obra) as mano_obra, mecanico_id')
        ->whereBetween('fecha', [$start->startOfDay(), $end->endOfDay()])
        ->when($mecId, fn($q) => $q->where('mecanico_id', $mecId))
        ->groupBy('date','mecanico_id')
        ->orderBy('date')
        ->get();

    $weeklyData = $raw->map(fn($item) => [
        'date'      => $item->date,
        'mano_obra' => (float) $item->mano_obra,
        'mecanico'  => $item->mecanico_id
            ? ($mechanics->firstWhere('id_mecanico', $item->mecanico_id)->nombre ?? '—')
            : '—',
        'pago'      => (float) $item->mano_obra * 0.70,
        'empresa'   => (float) $item->mano_obra * 0.30,
    ]);

    $mecSel = $mecId ? Mecanico::find($mecId) : null;
    return view('facturacion.pago_mecanico', compact(
        'weeklyData', 'mechanics', 'mecId', 'start', 'end'
    ));
}

public function reciboPdf(Request $request)
{
    $start    = Carbon::parse($request->input('start', now()->startOfWeek()));
    $end      = Carbon::parse($request->input('end',   now()->endOfWeek()));
    $mecId    = $request->input('mecanico_id');
    $mechanic = $mecId ? Mecanico::find($mecId) : null;

    $raw = Facturacion::selectRaw('DATE(fecha) as date, SUM(mano_obra) as mano_obra')
        ->whereBetween('fecha', [$start->startOfDay(), $end->endOfDay()])
        ->when($mecId, fn($q) => $q->where('mecanico_id', $mecId))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    $daily = $raw->map(fn($item) => [
        'date' => $item->date,
        'gain' => (float) $item->mano_obra * 0.70,
    ]);

    $total = $daily->sum('gain');

    $pdf = Pdf::loadView('facturacion.recibo_mecanico', compact(
        'daily', 'start', 'end', 'total', 'mechanic'
    ));

    return $pdf->download("recibo_mecanico_{$start->format('Ymd')}_{$end->format('Ymd')}.pdf");
}

}
