<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class InventarioController extends Controller
{
    /**
     * Consulta base de inventarios, aplica filtros de búsqueda, categoría y (opcional) stock bajo.
     */
    protected function consultaBaseInventarios(Request $request)
    {
        // Construimos la query inicial, cargando con la relación producto (incluye soft-deleted)
        $query = Inventario::with([
                'producto' => fn($q) => $q->withTrashed()
            ])
            // Filtro por nombre o descripción si viene 'filtro'
            ->when($request->filled('filtro'), fn($q) =>
                $q->whereHas('producto', fn($p) =>
                    $p->withTrashed()
                      ->where('nombre', 'like', "%{$request->filtro}%")
                      ->orWhere('descripcion', 'like', "%{$request->filtro}%")
                )
            )
            // Filtro por categoría si viene 'categoria_id'
            ->when($request->filled('categoria_id'), fn($q) =>
                $q->whereHas('producto', fn($p) =>
                    $p->withTrashed()
                      ->where('id_categoria', $request->categoria_id)
                )
            )
            ->orderBy('fecha_actualizacion', 'desc');

        // Obtenemos la colección completa
        $inventarios = $query->get();

        // Si el usuario marcó "solo_bajo", filtramos en memoria los que estén por debajo del stock mínimo
        if ($request->filled('solo_bajo')) {
            $inventarios = $inventarios->filter(fn($item) =>
                $item->cantidad_stock < $item->producto->stock_minimo
            );
        }

        return $inventarios;
    }

    /**
     * Mostrar la lista de inventarios (con filtros y posibilidad de “solo stock bajo”).
     */
    public function index(Request $request): View
    {
        $categorias   = Categoria::all();
        $inventarios  = $this->consultaBaseInventarios($request);

        return view('inventario', compact('inventarios', 'categorias'));
    }

    /**
     * Generar y descargar el PDF de inventario (respeta mismos filtros que en index).
     */
    public function exportPdf(Request $request)
    {
        $inventarios = $this->consultaBaseInventarios($request);

        $pdf = Pdf::loadView('inventario_pdf', compact('inventarios'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('inventario_actual.pdf');
    }
}
