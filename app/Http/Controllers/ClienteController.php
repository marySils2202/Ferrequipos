<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::all();
        return view('clientes', compact('clientes'));
    }

 public function store(Request $r)
{
    $r->validate([
        'nombre'    => 'required|string|max:100',
        'direccion' => 'nullable|string|max:150',
        'telefono'  => 'nullable|string|max:50',
    ]);

    $cliente = Cliente::create($r->only('nombre','direccion','telefono'));
    if ($r->expectsJson()) {
        return response()->json([
            'id_cliente' => $cliente->id_cliente,
            'nombre'     => $cliente->nombre,
        ]);
    }
    return back()->with('success','Cliente agregado.');
}

    public function update(Request $r, Cliente $cliente)
    {
        $r->validate(['nombre'=>'required']);
        $cliente->update($r->only('nombre','direccion','telefono'));
        return back()->with('success','Cliente actualizado.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return back()->with('success','Cliente eliminado.');
    }
}
