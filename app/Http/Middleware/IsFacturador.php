<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsFacturador
{
    public function handle($request, Closure $next)
    {
        if (auth()->user()->rol === 'facturador') {
            return $next($request);
        }

        abort(403, '');
    }
    
}
