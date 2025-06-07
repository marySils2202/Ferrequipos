<?php
// app/Http/Controllers/FiltrosController.php

namespace App\Http\Controllers;

use App\Models\{Cliente, Proveedor, Compra, Producto, Facturacion, Usuario, Credito};
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class FiltrosController extends Controller
{
    public function index(Request $request)
    {
        $tipo_reporte    = $request->query('tipo_reporte', '');
        $productos       = Producto::all();
        $seleccion       = $request->query('productos', []);
        $fecha           = $request->query('fecha', null);
        $cliente_nombre  = $request->query('cliente_nombre', '');
        $credito_estado  = $request->query('credito_estado', '');

        // Inicializamos
        $clientes    = collect();
        $usuarios    = collect();
        $proveedores = collect();
        $facturas    = collect();
        $compras     = collect();
        $productos   = Producto::all();
        $creditos    = collect();

        switch ($tipo_reporte) {
            case 'clientes':
                $clientes = Cliente::when($seleccion, fn($q) =>
                    $q->whereHas('facturas.detalles', fn($q2) =>
                        $q2->whereIn('id_producto', $seleccion)
                    )
                )->get();
                break;

            case 'usuarios':
                $usuarios = Usuario::all();
                break;

            case 'proveedores':
                $proveedores = Proveedor::when($seleccion, fn($q) =>
                    $q->whereHas('compras.detalles', fn($q2) =>
                        $q2->whereIn('id_producto', $seleccion)
                    )
                )->get();
                break;

            case 'facturas':
                $facturas = Facturacion::when($fecha, fn($q) =>
                        $q->whereDate('fecha', $fecha)
                    )
                    ->when($seleccion, fn($q) =>
                        $q->whereHas('detalles', fn($q2) =>
                            $q2->whereIn('id_producto', $seleccion)
                        )
                    )
                    ->get();
                break;

            case 'compras':
                $compras = Compra::when($fecha, fn($q) =>
                        $q->whereDate('fecha', $fecha)
                    )
                    ->when($seleccion, fn($q) =>
                        $q->whereHas('detalles', fn($q2) =>
                            $q2->whereIn('id_producto', $seleccion)
                        )
                    )
                    ->get();
                break;

            case 'productos':
                $productos = Producto::with('categoria')->get();
                break;

            case 'creditos':
                $creditos = Credito::with('factura.cliente')
                    // filtrar por nombre de cliente parcial
                    ->when($cliente_nombre, fn($q) =>
                        $q->whereHas('factura.cliente', fn($q2) =>
                            $q2->where('nombre', 'like', "%{$cliente_nombre}%")
                        )
                    )
                    // filtrar por estado
                    ->when($credito_estado === 'pendiente', fn($q) =>
                        $q->whereColumn('monto_pagado', '<', 'monto_total')
                    )
                    ->when($credito_estado === 'pagado', fn($q) =>
                        $q->whereColumn('monto_pagado', '>=', 'monto_total')
                    )
                    ->get();
                break;
        }

        return view('filtros_admin', [
            'tipo_reporte'     => $tipo_reporte,
            'productos'        => $productos,
            'fecha'            => $fecha,
            'seleccion'        => $seleccion,
            'cliente_nombre'   => $cliente_nombre,
            'credito_estado'   => $credito_estado,
            'clientes'         => $clientes,
            'usuarios'         => $usuarios,
            'proveedores'      => $proveedores,
            'facturas'         => $facturas,
            'compras'          => $compras,
            'creditos'         => $creditos,
        ]);
    }

public function exportPdf(Request $request)
{
    $tipo_reporte    = $request->query('tipo_reporte', '');
    $seleccion       = $request->query('productos', []);
    $fecha           = $request->query('fecha', null);
    $cliente_nombre  = $request->query('cliente_nombre', '');
    $credito_estado  = $request->query('credito_estado', '');

    switch ($tipo_reporte) {
        case 'clientes':
            $items  = Cliente::when($seleccion, fn($q) =>
                $q->whereHas('facturacion.detalles', fn($q2) =>
                    $q2->whereIn('id_producto', $seleccion)
                )
            )->get();
            $titulo = 'Listado de Clientes';
            $view   = 'pdf.reporte_admin';
            break;

        case 'usuarios':
            $items  = Usuario::all();
            $titulo = 'Listado de Usuarios';
            $view   = 'pdf.reporte_admin';
            break;

        case 'proveedores':
            $items  = Proveedor::when($seleccion, fn($q) =>
                $q->whereHas('compras.detalles', fn($q2) =>
                    $q2->whereIn('id_producto', $seleccion)
                )
            )->get();
            $titulo = 'Listado de Proveedores';
            $view   = 'pdf.reporte_admin';
            break;

        case 'facturas':
            $items  = Facturacion::when($fecha, fn($q) =>
                    $q->whereDate('fecha', $fecha)
                )
                ->when($seleccion, fn($q) =>
                    $q->whereHas('detalles', fn($q2) =>
                        $q2->whereIn('id_producto', $seleccion)
                    )
                )
                ->get();
            $titulo = "Facturas del día {$fecha}";
            $view   = 'pdf.reporte_admin';
            break;

        case 'compras':
            $items  = Compra::when($fecha, fn($q) =>
                    $q->whereDate('fecha', $fecha)
                )
                ->when($seleccion, fn($q) =>
                    $q->whereHas('detalles', fn($q2) =>
                        $q2->whereIn('id_producto', $seleccion)
                    )
                )
                ->get();
            $titulo = "Compras del día {$fecha}";
            $view   = 'pdf.reporte_admin';
            break;

        case 'productos':
            $items  = Producto::with('categoria')->get();
            $titulo = 'Listado de Productos';
            $view   = 'pdf.reporte_admin';
            break;

        case 'creditos':
            $items  = Credito::with('factura.cliente')
                ->when($cliente_nombre, fn($q) =>
                    $q->whereHas('factura.cliente', fn($q2) =>
                        $q2->where('nombre','like',"%{$cliente_nombre}%")
                    )
                )
                ->when($credito_estado === 'pendiente', fn($q) =>
                    $q->whereColumn('monto_pagado','<','monto_total')
                )
                ->when($credito_estado === 'pagado', fn($q) =>
                    $q->whereColumn('monto_pagado','>=','monto_total')
                )
                ->get();
            $titulo = 'Reporte de Créditos';
            $view   = 'pdf.reporte_creditos';
            break;

        default:
            abort(404, 'Tipo de reporte no válido');
    }

    $pdf = Pdf::loadView($view, [
        'titulo'       => $titulo,
        'tipo_reporte' => $tipo_reporte,
        'items'        => $items,
    ])->setPaper('a4', 'landscape');

    return $pdf->download("reporte_{$tipo_reporte}.pdf");
}

}
