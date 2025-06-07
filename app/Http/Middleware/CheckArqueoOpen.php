<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Arqueo;

class CheckArqueoOpen
{
  
    public function handle($request, Closure $next)
    {
        // Busca si existe un arqueo sin monto_final
        $abierto = Arqueo::whereNull('monto_final')->exists();

        if (! $abierto) {
            // Opcional: redirigir con mensaje
            return redirect()->route('arqueo.index')
                             ->with('error', 'Debes iniciar un arqueo antes de registrar datos.');
        }

        return $next($request);
    }
}
