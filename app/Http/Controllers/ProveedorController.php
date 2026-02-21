<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::all();
        return view('roles.bodeguero.agregar_proveedor', compact('proveedores'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|string|max:100',
            'domicilio' => 'nullable|string|max:150',
            'telefono'  => 'nullable|string|max:50',
        ]);

        Proveedor::create($request->only('nombre','domicilio','telefono'));

        return back()->with('success', 'Proveedor agregado correctamente.');
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $request->validate([
            'nombre'    => 'required|string|max:100',
            'domicilio' => 'nullable|string|max:150',
            'telefono'  => 'nullable|string|max:50',
        ]);

        $proveedor->update($request->only('nombre','domicilio','telefono'));

        return back()->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        if ($proveedor->compras()->count() > 0) {
            return back()->with('error', 
                'No se puede eliminar: este proveedor tiene compras registradas.'
            );
        }
        $proveedor->delete();
    
        return back()->with('success', 'Proveedor eliminado correctamente.');
    }
}
