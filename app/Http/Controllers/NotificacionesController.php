<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\Compra;
use App\Models\Facturacion;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Credito;   

class NotificacionesController extends Controller
{
    public function index()
    {

        $alertas = Inventario::with('producto')
            ->whereHas('producto', function($q) {
                $q->whereColumn('inventario.cantidad_stock', '<', 'productos.stock_minimo');
            })
            ->get();

        $ultimaCompra = Compra::latest('fecha')->first();
        $ultima = $ultimaCompra
            ? $ultimaCompra->fecha->format('d/m/Y H:i:s')
            : null;


        $ultimaFactura = Facturacion::with(['cliente', 'usuario'])
            ->latest('fecha')
            ->first();

        $ultimoProveedor = Proveedor::latest('id_proveedor')->first();
        $ultimoProducto  = Producto::latest('id_producto')->first();
        $ultimoCliente   = Cliente::latest('id_cliente')->first();

        $ultimoCredito = Credito::with('factura.cliente')
            ->latest('id_credito')
            ->first();

        return view('roles.admin.notificaciones', compact(
            'alertas',
            'ultima',
            'ultimaFactura',
            'ultimoProveedor',
            'ultimoProducto',
            'ultimoCliente',
            'ultimoCredito'      
        ));
    }
}
