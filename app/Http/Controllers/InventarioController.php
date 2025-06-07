<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class InventarioController extends Controller
{
    protected function consultaBaseInventarios(Request $request)
    {
        $query = Inventario::with([
                'producto' => fn($q) => $q->withTrashed()
            ])
            ->when($request->filled('filtro'), fn($q) =>
                $q->whereHas('producto', fn($p) =>
                    $p->withTrashed()
                      ->where('nombre', 'like', "%{$request->filtro}%")
                      ->orWhere('descripcion', 'like', "%{$request->filtro}%")
                )
            )
            ->when($request->filled('categoria_id'), fn($q) =>
                $q->whereHas('producto', fn($p) =>
                    $p->withTrashed()
                      ->where('id_categoria', $request->categoria_id)
                )
            )
            ->orderBy('fecha_actualizacion', 'desc');

        $inventarios = $query->get();
        if ($request->filled('solo_bajo')) {
            $inventarios = $inventarios->filter(fn($item) =>
                $item->cantidad_stock < $item->producto->stock_minimo
            );
        }

        return $inventarios;
    }
     public function index(Request $request): View
    {
        $categorias   = Categoria::all();
        $inventarios  = $this->consultaBaseInventarios($request);

        return view('inventario', compact('inventarios', 'categorias'));
    }
    public function exportPdf(Request $request)
    {
        $inventarios = $this->consultaBaseInventarios($request);

        $pdf = Pdf::loadView('inventario_pdf', compact('inventarios'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('inventario_actual.pdf');
    }
}
