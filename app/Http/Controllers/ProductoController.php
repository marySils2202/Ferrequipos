<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\DetalleCompra;
use Illuminate\Http\Request;

class ProductoController extends Controller
{

public function index()
{
    $productos  = Producto::with('categoria')->get();

    $categorias = Categoria::all();

    return view('agregar_producto', compact('productos', 'categorias'));
}



    public function store(Request $request)
    {
        $request->validate([
            'nombre'        => 'required|string',
            'descripcion'   => 'nullable|string',
            'id_categoria'  => 'required|exists:categorias,id_categoria',
            'estado'        => 'required|in:0,1',
            'precio_venta'  => 'required|numeric|min:0',
            'stock_minimo'  => 'required|integer|min:0',
        ]);

        Producto::create($request->only([
            'nombre',
            'descripcion',
            'id_categoria',
            'estado',
            'precio_venta',
            'stock_minimo',
        ]));

        return back()->with('success', 'Producto creado correctamente.');
    }




 public function update(Request $request, Producto $producto)
{
    $request->validate([
        'nombre'        => 'required|string',
        'descripcion'   => 'nullable|string',
        'id_categoria'  => 'required|exists:categorias,id_categoria',
        'estado'        => 'required|in:0,1',
        'precio_venta'  => 'required|numeric|min:0',
        'stock_minimo'  => 'required|integer|min:0',
    ]);

    $ultimo = DetalleCompra::where('id_producto', $producto->id_producto)
                           ->orderByDesc('id_detalle')
                           ->first();

    $nuevoPrecio = (float) $request->precio_venta;
    $viejoPrecio = (float) $producto->precio_venta;

    if ($ultimo && $nuevoPrecio !== $viejoPrecio
        && $nuevoPrecio <= (float) $ultimo->precio_unitario) {
        $msg = 'El precio de venta debe ser mayor que el último precio de compra (₡'
             . number_format($ultimo->precio_unitario,2) . ').';
        if ($request->wantsJson()) {
            return response()->json(['error' => $msg], 422);
        }
        return back()->withInput()->withErrors(['precio_venta' => $msg]);
    }

    $producto->update($request->only([
        'nombre',
        'descripcion',
        'id_categoria',
        'estado',
        'precio_venta',
        'stock_minimo',
    ]));

    $producto->load('categoria');

    if ($request->wantsJson()) {
        return response()->json($producto, 200);
    }

    return back()->with('success', 'Producto actualizado correctamente.');
}



    public function destroy(Request $request, Producto $producto)
    {
        if ($producto->detallesCompras()->exists()) {
            $msg = 'No se puede eliminar: este producto tiene compras registradas.';
            if ($request->wantsJson()) {
                return response()->json(['error' => $msg], 400);
            }
            return back()->with('error',$msg);
        }

        $producto->delete();
        $msg = 'Producto eliminado correctamente.';

        if ($request->wantsJson()) {
            return response()->json(['success' => $msg], 200);
        }

        return back()->with('success',$msg);
    }


}
