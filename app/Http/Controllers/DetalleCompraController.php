<?php

namespace App\Http\Controllers;

use App\Models\DetalleCompra;
use Illuminate\Http\Request;

class DetalleCompraController extends Controller
{

    public function index()
    {
        
    }
    public function create()
    {
    
    }

    public function store(Request $request)
    {
        
    }

    public function show(DetalleCompra $detalleCompra)
    {
    
    }

    public function edit(DetalleCompra $detalleCompra)
    {
    
    }


    public function update(Request $request, DetalleCompra $detalleCompra)
    {
        
    }


    public function destroy(DetalleCompra $detalleCompra)
    {
        
    }


    public function detalleCompras()
    {
        $detalles = DetalleCompra::with('producto')->get();
        return view('detalle_compras', compact('detalles'));
    }
    
    
}
