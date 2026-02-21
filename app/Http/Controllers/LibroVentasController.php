<?php

namespace App\Http\Controllers;

use App\Models\Arqueo;
use App\Models\LibroVenta;
use App\Models\Facturacion;
use Illuminate\Http\Request;

class LibroVentasController extends Controller
{
    public function index()
    {

        $ventas = LibroVenta::with('arqueo.impresor')
                            ->orderByDesc('fecha')
                            ->get();

        foreach ($ventas as $venta) {

            $facturas = Facturacion::with('detalles.producto.detallesCompras')
                                   ->whereDate('fecha', $venta->fecha)
                                   ->get();

            $gananciaDiaria = 0;

            foreach ($facturas as $factura) {
                foreach ($factura->detalles as $detalle) {

                    $precioVenta = $detalle->producto->precio_venta;


                    $precioCompraPromedio = $detalle->producto
                                                    ->detallesCompras
                                                    ->avg('precio_unitario') ?? 0;


                    $cantidadVendida = $detalle->cantidad;

                    $gananciaDiaria += ($precioVenta - $precioCompraPromedio) * $cantidadVendida;
                }
            }


            $venta->ganancia_diaria = $gananciaDiaria;
        }

        return view('roles.facturador.libro_ventas', compact('ventas'));
    }
}
