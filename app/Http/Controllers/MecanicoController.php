<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mecanico;

class MecanicoController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100|unique:mecanicos,nombre',
        ]);

        Mecanico::create($data);

        return back()->with('success', "Mecánico “{$data['nombre']}” agregado.");
    }

    public function destroy(Mecanico $mecanico)
    {
        $nombre = $mecanico->nombre;
        $mecanico->delete();

        return back()->with('success', "Mecánico “{$nombre}” eliminado.");
    }
}
