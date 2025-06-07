<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsBodeguero
{
    public function handle($request, Closure $next)
    {
        if (auth()->user()->rol === 'bodeguero') {
            return $next($request);
        }
 
        abort(403, '');
    }
    
    
}
