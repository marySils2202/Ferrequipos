<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Compra;
use App\Models\Movimiento;
use App\Models\DetalleCompra;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\Inventario;

class CompraController extends Controller
{
    public function create()
    {
        $proveedores = Proveedor::all();
        $productos = Producto::all();
        $usuario = auth()->user();
        return view('roles.bodeguero.compras', compact('proveedores', 'productos', 'usuario'));
    }
public function store(Request $request)
{
    $mensajes = [
        'id_proveedor.required'    => 'Debes seleccionar un proveedor.',
        'id_proveedor.exists'      => 'El proveedor seleccionado no es válido.',
        'id_usuario.required'      => 'El usuario es obligatorio.',
        'id_usuario.exists'        => 'El usuario seleccionado no es válido.',
        'id_producto.required'     => 'Debes seleccionar un producto.',
        'id_producto.exists'       => 'El producto seleccionado no es válido.',
        'cantidad.required'        => 'El campo cantidad es obligatorio.',
        'cantidad.integer'         => 'La cantidad debe ser un número entero.',
        'cantidad.min'             => 'La cantidad mínima es 1.',
        'precio_unitario.required' => 'Debes ingresar el precio unitario.',
        'precio_unitario.numeric'  => 'El precio unitario debe ser un número.',
        'precio_unitario.min'      => 'El precio unitario no puede ser negativo.',
    ];
    $data = $request->validate([
        'id_proveedor'    => 'required|exists:proveedores,id_proveedor',
        'id_usuario'      => 'required|exists:usuarios,id_usuario',
        'id_producto'     => 'required|exists:productos,id_producto',
        'cantidad'        => 'required|integer|min:1',
        'precio_unitario' => 'required|numeric|min:0',
    ], $mensajes);
    $producto = Producto::findOrFail($data['id_producto']);
    if ($data['precio_unitario'] > $producto->precio_venta) {
        return back()
            ->withInput()
            ->withErrors([
                'precio_unitario' => 'El precio de compra (₡' . number_format($data['precio_unitario'], 2) . ') '
                    . 'no puede ser mayor que el precio de venta (₡' . number_format($producto->precio_venta, 2) . ').'
            ]);
    }
    $total = $data['cantidad'] * $data['precio_unitario'];
    DB::transaction(function() use ($data, $total) {
        $compra = Compra::create([
            'id_proveedor' => $data['id_proveedor'],
            'id_usuario'   => $data['id_usuario'],
            'cantidad'     => $data['cantidad'],
            'total'        => $total,
        ]);
        $compra->detalles()->create([
            'id_producto'     => $data['id_producto'],
            'cantidad'        => $data['cantidad'],
            'precio_unitario' => $data['precio_unitario'],
            'subtotal'        => $total,
        ]);
        $inv = Inventario::firstOrNew(['id_producto' => $data['id_producto']]);
        $inv->cantidad_stock      = ($inv->exists ? $inv->cantidad_stock : 0) + $data['cantidad'];
        $inv->fecha_actualizacion = now();
        $inv->save();
        Movimiento::create([
    'id_producto' => $data['id_producto'],
    'tipo'        => 'entrada',
    'cantidad'    => $data['cantidad'],
    'descripcion' => 'Compra #' . $compra->id_compra,
]);
    });
    if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
            'message' => 'Compra registrada con éxito.'
        ]);
    }
    return redirect()
        ->route('factura')
        ->with('success', '¡Compra registrada y stock actualizado correctamente!');
}
    public function detalleCompras()
    {
        $detalles = DetalleCompra::with([
                        'producto',
                        'compra.proveedor',
                        'compra.usuario'
                    ])->get();

        return view('roles.bodeguero.detalle_compras', compact('detalles'));
    }
    public function verInventario()
    {
        $inventario = Inventario::with('producto')->get();
        return view('roles.bodeguero.inventario', compact('inventario'));
    }
    public function actualizarPrecio(Request $request, $id)
    {
        $request->validate([
            'precio' => 'required|numeric|min:0',
        ]);

        $inventario = Inventario::findOrFail($id);
        $inventario->precio              = $request->precio;
        $inventario->fecha_actualizacion = now();
        $inventario->save();

        return back()->with('success', 'Precio actualizado correctamente.');
    }
}
