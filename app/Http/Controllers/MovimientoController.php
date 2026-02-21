<?php
namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\DetalleCompra;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class MovimientoController extends Controller
{

    public function index(Request $request)
    {

        $productos  = Producto::pluck('nombre','id_producto');
        $categorias = Categoria::pluck('nombre_categoria','id_categoria');
        $statsComprasPorProducto = DetalleCompra::select(
                'id_producto',
                DB::raw('MIN(precio_unitario) AS min_precio'),
                DB::raw('MAX(precio_unitario) AS max_precio'),
                DB::raw('AVG(precio_unitario) AS avg_precio'),
                DB::raw('COUNT(*) AS num_registros')
            )
            ->when($request->filled('nombre_producto'), fn($q) =>
                $q->whereHas('producto', fn($q2)=>
                    $q2->where('nombre','like',"%{$request->nombre_producto}%")
                )
            )
            ->when($request->filled('categoria_id'), fn($q) =>
                $q->whereHas('producto', fn($q2)=>
                    $q2->where('id_categoria',$request->categoria_id)
                )
            )
            ->groupBy('id_producto')
            ->with('producto')
            ->get();

        $movimientos = Movimiento::with('producto')
                                  ->orderByDesc('fecha')
                                  ->get();

        return view('roles.admin.movimientos', compact(
            'productos','categorias',
            'statsComprasPorProducto','movimientos'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_producto' => 'required|exists:productos,id_producto',
            'tipo'        => 'required|in:entrada,salida',
            'cantidad'    => 'required|integer|min:1',
            'descripcion' => 'nullable|string|max:255',
        ]);

        DB::transaction(function() use ($data) {
            Movimiento::create($data);
            $inv = Inventario::firstOrNew(['id_producto'=>$data['id_producto']]);
            $inv->cantidad_stock = ($data['tipo']==='entrada')
                ? ($inv->exists ? $inv->cantidad_stock : 0) + $data['cantidad']
                : max(0, $inv->cantidad_stock - $data['cantidad']);
            $inv->fecha_actualizacion = now();
            $inv->save();
        });

        return redirect()
               ->route('movimientos.index')
               ->with('success','Movimiento registrado correctamente.');
    }

    public function exportPdf(Request $request)
{
    $movimientos = Movimiento::with('producto')
        ->when($request->filled('filtro'), function($q) use($request) {
            $f = $request->filtro;
            $q->where(function($q2) use($f) {
                $q2->where('producto.nombre', 'like', "%{$f}%")
                   ->orWhere('descripcion',   'like', "%{$f}%");
            })->orWhereHas('producto', fn($q3) =>
                $q3->where('nombre','like',"%{$f}%")
            );
        })
        ->when($request->filled('tipo'), function($q) use($request) {
            $q->where('tipo', $request->tipo);
        })
        ->when($request->filled('fecha'), function($q) use($request) {
            $q->whereDate('fecha', $request->fecha);
        })
        ->orderByDesc('fecha')
        ->get();

    $pdf = Pdf::loadView('movimientos_pdf', compact('movimientos'))
              ->setPaper('a4','landscape');

    return $pdf->download('movimientos.pdf');
}

    public function exportStatsPdf(Request $request)
    {
        $nombre    = $request->query('nombre_producto','');
        $categoria = $request->query('categoria_id','');

        $statsComprasPorProducto = DetalleCompra::select(
                'id_producto',
                DB::raw('MIN(precio_unitario) AS min_precio'),
                DB::raw('MAX(precio_unitario) AS max_precio'),
                DB::raw('AVG(precio_unitario) AS avg_precio'),
                DB::raw('COUNT(*) AS num_registros')
            )
            ->when($nombre, fn($q)=>
                $q->whereHas('producto', fn($q2)=>
                    $q2->where('nombre','like',"%{$nombre}%")
                )
            )
            ->when($categoria, fn($q)=>
                $q->whereHas('producto', fn($q2)=>
                    $q2->where('id_categoria',$categoria)
                )
            )
            ->groupBy('id_producto')
            ->with('producto')
            ->get();

        $labelProd = $nombre ?: 'Todos';
        $labelCat  = $categoria
                   ? Categoria::find($categoria)->nombre_categoria
                   : 'Todas';

        $pdf = Pdf::loadView(
            'pdf.stats_compras',
            compact('statsComprasPorProducto','labelProd','labelCat')
        )
        ->setPaper('a4','portrait');

        return $pdf->download("estadisticas_{$labelProd}_{$labelCat}.pdf");
    }
}
