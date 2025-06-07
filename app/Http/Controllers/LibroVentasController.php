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
        // Traer todos los registros de LibroVenta con su arqueo e impresor cargados
        $ventas = LibroVenta::with('arqueo.impresor')
                            ->orderByDesc('fecha')
                            ->get();

        // Para cada registro de LibroVenta, calcular la ganancia diaria
        foreach ($ventas as $venta) {
            // Obtener todas las facturas que correspondan a la misma fecha del LibroVenta
            $facturas = Facturacion::with('detalles.producto.detallesCompras')
                                   ->whereDate('fecha', $venta->fecha)
                                   ->get();

            $gananciaDiaria = 0;

            foreach ($facturas as $factura) {
                foreach ($factura->detalles as $detalle) {
                    // Precio de venta por unidad (tomado desde el producto)
                    $precioVenta = $detalle->producto->precio_venta;

                    // Costo de compra promedio: promedio de los precios en detalle_compra
                    $precioCompraPromedio = $detalle->producto
                                                    ->detallesCompras
                                                    ->avg('precio_unitario') ?? 0;

                    // Cantidad vendida de este detalle
                    $cantidadVendida = $detalle->cantidad;

                    // Sumar la diferencia (venta − compra) × cantidad
                    $gananciaDiaria += ($precioVenta - $precioCompraPromedio) * $cantidadVendida;
                }
            }

            // Adjuntar el valor calculado al objeto LibroVenta
            $venta->ganancia_diaria = $gananciaDiaria;
        }

        return view('libro_ventas', compact('ventas'));
    }
}
