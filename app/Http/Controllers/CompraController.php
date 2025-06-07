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
    /**
     * Muestra el formulario para registrar una compra.
     * Aquí simplemente recuperamos todos los proveedores y todos los productos,
     * asegurándonos de que cada $prod tenga también su campo "descripcion".
     */
    public function create()
    {
        // Todos los proveedores para llenar el <select>
        $proveedores = Proveedor::all();

        // Todos los productos: al llamar Producto::all(), el atributo "descripcion"
        // ya viene incluido automáticamente (suponiendo que está en la tabla).
        // Con esto, en la vista podremos acceder a $prod->descripcion para cada opción.
        $productos = Producto::all();

        // Usuario logueado (para mostrar su nombre y enviar su id oculto)
        $usuario = auth()->user();

        // Devolvemos la vista "compras" (o "compras.registrar", según tu estructura),
        // pasándole las variables $proveedores, $productos y $usuario.
        return view('compras', compact('proveedores', 'productos', 'usuario'));
    }

    /**
     * Almacena en base de datos la compra y su detalle, y actualiza inventario.
     * También valida que el precio_unitario no supere el precio de venta del producto.
     */
public function store(Request $request)
{
    // Mensajes de validación en español
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

    // Validación básica de los campos con mensajes en español
    $data = $request->validate([
        'id_proveedor'    => 'required|exists:proveedores,id_proveedor',
        'id_usuario'      => 'required|exists:usuarios,id_usuario',
        'id_producto'     => 'required|exists:productos,id_producto',
        'cantidad'        => 'required|integer|min:1',
        'precio_unitario' => 'required|numeric|min:0',
    ], $mensajes);

    // Buscamos el producto para comparar su precio_venta
    $producto = Producto::findOrFail($data['id_producto']);

    // Si el precio de compra es mayor al precio de venta, devolvemos error y volvemos atrás
    if ($data['precio_unitario'] > $producto->precio_venta) {
        return back()
            ->withInput()
            ->withErrors([
                'precio_unitario' => 'El precio de compra (₡' . number_format($data['precio_unitario'], 2) . ') '
                    . 'no puede ser mayor que el precio de venta (₡' . number_format($producto->precio_venta, 2) . ').'
            ]);
    }

    // Calculamos el total (cantidad × precio_unitario)
    $total = $data['cantidad'] * $data['precio_unitario'];

    // Hacemos toda la inserción en una transacción
    DB::transaction(function() use ($data, $total) {
        // 1) Creamos la cabecera de compra
        $compra = Compra::create([
            'id_proveedor' => $data['id_proveedor'],
            'id_usuario'   => $data['id_usuario'],
            'cantidad'     => $data['cantidad'],
            'total'        => $total,
        ]);

        // 2) Creamos el detalle de compra asociado
        $compra->detalles()->create([
            'id_producto'     => $data['id_producto'],
            'cantidad'        => $data['cantidad'],
            'precio_unitario' => $data['precio_unitario'],
            'subtotal'        => $total,
        ]);

        // 3) Actualizamos (o insertamos) el inventario de ese producto
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

    // Si la petición vino por AJAX, devolvemos JSON
    if ($request->ajax() || $request->wantsJson()) {
        return response()->json([
            'message' => 'Compra registrada con éxito.'
        ]);
    }

    // Sino, redirigimos a facturación con mensaje de éxito
    return redirect()
        ->route('factura')
        ->with('success', '¡Compra registrada y stock actualizado correctamente!');
}


    /**
     * Muestra un listado de detalles de todas las compras realizadas.
     */
    public function detalleCompras()
    {
        $detalles = DetalleCompra::with([
                        'producto',
                        'compra.proveedor',
                        'compra.usuario'
                    ])->get();

        return view('detalle_compras', compact('detalles'));
    }

    /**
     * Muestra el inventario completo (producto + cantidad en stock).
     */
    public function verInventario()
    {
        $inventario = Inventario::with('producto')->get();
        return view('inventario', compact('inventario'));
    }

    /**
     * Permite actualizar el precio en el inventario de un producto dado.
     */
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
