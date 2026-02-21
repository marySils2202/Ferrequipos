<?php

namespace App\Http\Controllers;

use App\Models\Arqueo;
use App\Models\LibroVenta;
use App\Models\Facturacion;
use Illuminate\Http\Request;
use Carbon\Carbon;

use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ArqueoController extends Controller
{
   
public function index()
{
   
    $arqueos = Arqueo::orderByDesc('id_arqueo')->get();
    $abierto = Arqueo::whereNull('monto_final')->first();
    $totalVentas = null;
    if ($abierto) {
        $now = Carbon::now();
        if ($now->hour >= 6) {
            $start = Carbon::today()->setTime(6, 0);
            $end   = Carbon::tomorrow()->setTime(6, 0);
        } else {
            $start = Carbon::yesterday()->setTime(6, 0);
            $end   = Carbon::today()->setTime(6, 0);
        }

        $totalVentas = Facturacion::whereBetween('fecha', [$start, $end])
                                  ->sum('total');
    }

    $ultimoCierre = Arqueo::whereNotNull('monto_final')
                         ->latest('id_arqueo')
                         ->value('monto_final')
                   ?? 0;

    $montoFinalCalculado = $abierto
        ? round($abierto->monto_inicial + $totalVentas, 2)
        : null;

    return view('arqueo', compact(
        'arqueos',
        'abierto',
        'totalVentas',
        'ultimoCierre',
        'montoFinalCalculado'
    ));
}

    public function store(Request $request)
    {
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
        ]);

        Arqueo::create([
            'fecha'         => Carbon::now(),
            'monto_inicial' => $request->monto_inicial,
            'salida_caja'   => 0,
            'monto_final'   => null,
            'razon_salida'  => null,
        ]);

        return redirect()->route('arqueo.index')
                         ->with('success', 'Arqueo iniciado correctamente.');
    }

public function edit($id)
{
    $arqueos = Arqueo::orderByDesc('id_arqueo')->get();
    $abierto = Arqueo::whereNull('monto_final')->first();
    $editArqueo = Arqueo::findOrFail($id);

    if (is_null($editArqueo->monto_final)) {
        return redirect()->route('arqueo.index')
                         ->with('error','Solo puedes editar arqueos ya cerrados.');
    }

  
    $totalVentas = null;
    if ($abierto) {
        $now = Carbon::now();
        if ($now->hour >= 6) {
            $start = Carbon::today()->setTime(6,0);
            $end   = Carbon::tomorrow()->setTime(6,0);
        } else {
            $start = Carbon::yesterday()->setTime(6,0);
            $end   = Carbon::today()->setTime(6,0);
        }
        $totalVentas = Facturacion::whereBetween('fecha', [$start,$end])->sum('total');
    }
    $ultimoCierre = Arqueo::whereNotNull('monto_final')
                         ->latest('id_arqueo')
                         ->value('monto_final')
                   ?? 0;
    $montoFinalCalculado = $abierto
        ? round($abierto->monto_inicial + $totalVentas, 2)
        : null;
    // ------------------------------------------------------------

    return view('arqueo', compact(
        'arqueos',
        'abierto',
        'editArqueo',
        'totalVentas',
        'ultimoCierre',
        'montoFinalCalculado'
    ));
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'monto_final'  => 'required|numeric|min:0',
            'salida_caja'  => 'nullable|numeric|min:0',
            'razon_salida' => 'nullable|string|max:255',
        ]);

        $arqueo = Arqueo::findOrFail($id);

        $arqueo->update([
            'monto_final'  => $request->monto_final,
            'salida_caja'  => $request->salida_caja ?? 0,
            'razon_salida' => $request->razon_salida,
        ]);
        $libro = LibroVenta::where('id_arqueo', $arqueo->id_arqueo)->first();
        if ($libro) {
            $libro->update([
                'fecha'       => $arqueo->printed_at ?? now(),
                'monto_final' => $arqueo->monto_final,
            ]);
        }

        return redirect()->route('arqueo.index')
                         ->with('success', 'Arqueo actualizado correctamente.');
    }

    public function cerrar(Request $request, $id)
    {
        $request->validate([
            'monto_final'  => 'required|numeric',
            'salida_caja'  => 'nullable|numeric',
            'razon_salida' => 'nullable|string|max:255',
        ]);

        $arq = Arqueo::findOrFail($id);

        $arq->update([
            'monto_final'   => $request->monto_final,
            'salida_caja'   => $request->salida_caja ?? 0,
            'razon_salida'  => $request->razon_salida,
            'printed_by'    => auth()->id(),
            'printed_at'    => now(),
        ]);

   LibroVenta::create([
    'fecha'       => $arq->printed_at,
    'monto_final' => $arq->monto_final,
    'id_arqueo'   => $arq->id_arqueo,
]);

        return redirect()->route('arqueo.index')
                         ->with('success', 'Arqueo cerrado correctamente.')
                         ->with('print_id', $arq->id_arqueo);
    }

public function pdf()
{
    $now = Carbon::now();
    if ($now->hour >= 6) {
        $start = Carbon::today()->setTime(6, 0);
        $end   = Carbon::tomorrow()->setTime(6, 0);
    } else {
        $start = Carbon::yesterday()->setTime(6, 0);
        $end   = Carbon::today()->setTime(6, 0);
    }
    $facturas = Facturacion::with('cliente')
        ->whereBetween('fecha', [$start, $end])
        ->get();

    $totalesPago = [
        'efectivo' => 0,
        'tarjeta'  => 0,
        'credito'  => 0,
    ];
    foreach ($facturas as $f) {
        $metodo = $f->metodo_pago ?? 'efectivo';
        if (isset($totalesPago[$metodo])) {
            $totalesPago[$metodo] += $f->total;
        }
    }

    $totalVentas = array_sum($totalesPago);
    $totalDescuentos = $facturas->sum('descuento');
    $rangoLabel = $start->format('Y-m-d H:i') . ' – ' . $end->format('Y-m-d H:i');
    $pdf = PDF::loadView('pdf.reporte_ventas', [
        'facturas'         => $facturas,
        'totalVentas'      => $totalVentas,
        'totalesPago'      => $totalesPago,
        'rangoLabel'       => $rangoLabel,
        'totalDescuentos'  => $totalDescuentos,   
    ]);

    return $pdf->download("ventas_{$start->format('Ymd_His')}.pdf");
}

    public function print($id)
    {
        $arqueo = Arqueo::findOrFail($id);

        $arqueo->update([
            'printed_by' => auth()->id(),
            'printed_at' => now(),
        ]);

        $pdf = PDF::loadView('pdf.arqueo_cierre', [
            'arqueo'  => $arqueo,
            'usuario' => auth()->user(),
        ]);

        return $pdf->download("arqueo_cierre_{$arqueo->id_arqueo}.pdf");
    }
    
}
