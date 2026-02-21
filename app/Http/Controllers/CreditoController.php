<?php
// app/Http/Controllers/CreditoController.php

namespace App\Http\Controllers;

use App\Models\{Credito, Facturacion, Producto, Cliente};
use Illuminate\Http\Request;
use App\Models\Inventario;
use Illuminate\Support\Facades\DB;

class CreditoController extends Controller
{
   
  public function index()
    {
        $creditos = Credito::with('factura.cliente')
                           ->orderBy('estado')
                           ->get();

        $creditosPendientes = $creditos
            ->filter(fn($c) => $c->saldo > 0)
            ->map(fn($c) => [
                'id_credito' => $c->id_credito,
                'cliente'    => $c->factura->cliente,
                'saldo'      => $c->saldo,
            ]);
        $inventarios = Inventario::with('producto')
                                 ->where('cantidad_stock', '>', 0)
                                 ->get();
        $clientes = Cliente::all();
        return view('roles.facturador.creditos', [
            'creditos'            => $creditos,
            'creditosPendientes'  => $creditosPendientes,
            'inventarios'         => $inventarios,
            'clientes'            => $clientes,
        ]);
    }

    public function show($id)
    {

        $creditos = Credito::with('factura.cliente')
                           ->orderBy('estado')
                           ->get();

        $credito = Credito::with('factura.cliente')
                          ->findOrFail($id);


        $inventarios = Inventario::with('producto')
                                 ->where('cantidad_stock', '>', 0)
                                 ->get();
        $clientes = Cliente::all();

        return view('roles.facturador.creditos', compact(
            'creditos',
            'credito',
            'inventarios',
            'clientes'
        ));
    }

public function store(Request $req)
{
    $data = $req->validate([
        'id_cliente'              => 'required|exists:clientes,id_cliente',
        'productos'               => 'required|array|min:1',
        'productos.*.id_producto' => 'required|exists:productos,id_producto',
        'productos.*.cantidad'    => 'required|integer|min:1',
    ]);

    DB::transaction(function () use ($data) {

        $total = 0;
        foreach ($data['productos'] as $prod) {
            $p      = DB::table('productos')->where('id_producto', $prod['id_producto'])->first();
            $total += $p->precio_venta * $prod['cantidad'];
        }

        $facturaId = DB::table('facturacion')->insertGetId([
            'id_cliente'      => $data['id_cliente'],
            'id_usuario'      => auth()->id(),
            'total'           => $total,
            'monto_pago'      => 0,
            'vuelto'          => 0,
            'mano_obra'       => 0,
            'metodo_pago'     => 'credito',
            'saldo_pendiente' => $total,
        ]);
        foreach ($data['productos'] as $prod) {
            DB::table('detalle_factura')->insert([
                'id_factura' => $facturaId,
                'id_producto' => $prod['id_producto'],
                'cantidad'    => $prod['cantidad'],
            ]);
            DB::table('inventario')
                ->where('id_producto', $prod['id_producto'])
                ->update([
                    'cantidad_stock'      => DB::raw("GREATEST(0, cantidad_stock - {$prod['cantidad']})"),
                    'fecha_actualizacion' => now(),
                ]);
            DB::table('movimientos')->insert([
                'id_producto' => $prod['id_producto'],
                'tipo'        => 'salida',
                'cantidad'    => $prod['cantidad'],
                'descripcion' => 'Factura #' . $facturaId,
                'fecha'       => now(),
            ]);
        }
        DB::table('creditos')->insert([
            'id_factura'   => $facturaId,
            'monto_total'  => $total,
            'monto_pagado' => 0,
            'estado'       => 'pendiente',
        ]);
    });

    return redirect()
           ->route('creditos.index')
           ->with('success', '✅ Crédito creado correctamente.');
}


public function abonar(Request $request, $id)
{
    $request->validate([
        'monto_abono' => 'required|numeric|min:0.01',
    ]);

    $credito = Credito::findOrFail($id);
    $abono   = $request->input('monto_abono');

    if ($credito->monto_pagado + $abono > $credito->monto_total) {
        return back()->withErrors([
            'monto_abono' => 'El abono supera el saldo pendiente.',
        ]);
    }

    $credito->monto_pagado += $abono;
    $saldoRestante          = $credito->monto_total - $credito->monto_pagado;

    if ($saldoRestante <= 0) {
        $credito->estado       = Credito::EST_PAGADO;
        $credito->monto_pagado = $credito->monto_total;
        $flashMsg              = "✅ Crédito #{$id} pagado y cancelado.";
        $printInvoiceId        = $credito->id_factura;
    } else {
        $flashMsg = "Abono de C\$ {$abono} registrado.";
    }
    $credito->save();
    Facturacion::where('id_factura', $credito->id_factura)
               ->update([
                   'saldo_pendiente' => max(0, $saldoRestante),
                   'monto_pago'      => $credito->monto_pagado,
               ]);
    $redirect = redirect()
        ->route('creditos.index')
        ->with('success', $flashMsg);

    if (isset($printInvoiceId)) {
        $redirect->with('print_invoice_id', $printInvoiceId);
    }

    return $redirect;
}
public function cancelar($id)
{
    DB::transaction(function () use ($id, &$facturaId) {
        $credito   = Credito::findOrFail($id);
        $facturaId = $credito->id_factura;
        Facturacion::where('id_factura', $facturaId)->delete();
        $credito->delete();
    });

    return redirect()
           ->route('creditos.index')
           ->with([
             'success'           => "✅ Crédito #{$id} cancelado.",
             'print_invoice_id'  => $facturaId,
           ]);


           
}
public function reembolsoCredito($id)
{
    DB::transaction(function () use ($id) {
        $credito = DB::table('creditos')->where('id_credito', $id)->first();
        if (!$credito) abort(404, 'Crédito no encontrado.');

        $facturaId = $credito->id_factura;

        $detalles = DB::table('detalle_factura')->where('id_factura', $facturaId)->get();
        foreach ($detalles as $detalle) {
            $inventario = DB::table('inventario')->where('id_producto', $detalle->id_producto)->first();
            if ($inventario) {
                DB::table('inventario')
                    ->where('id_producto', $detalle->id_producto)
                    ->update([
                        'cantidad_stock'      => $inventario->cantidad_stock + $detalle->cantidad,
                        'fecha_actualizacion' => now(),
                    ]);
            } else {
                DB::table('inventario')->insert([
                    'id_producto'          => $detalle->id_producto,
                    'cantidad_stock'       => $detalle->cantidad,
                    'fecha_actualizacion'  => now(),
                ]);
            }
            DB::table('movimientos')->insert([
                'id_producto' => $detalle->id_producto,
                'tipo'        => 'entrada',
                'cantidad'    => $detalle->cantidad,
                'descripcion' => 'Reembolso Crédito / Factura #' . $facturaId,
                'fecha'       => now(),
            ]);
        }
        DB::table('detalle_factura')->where('id_factura', $facturaId)->delete();
        DB::table('facturacion')->where('id_factura', $facturaId)->delete();

        DB::table('creditos')->where('id_credito', $id)->delete();
    });

    return redirect()
        ->route('creditos.index')
        ->with('success', "✅ Crédito #{$id} reembolsado y stock restituido.");
}





}



