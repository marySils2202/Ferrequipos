<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class StripQueryStrings
{
    public function handle(Request $request, Closure $next)
    {
        /** Primero dejamos que el controlador renderice la vista */
        $response = $next($request);

        // Solo GET, solo HTML, solo si hay query string
        if (
            $request->isMethod('GET') &&
            $request->getQueryString() &&
            $response instanceof Response &&
            Str::contains($response->headers->get('Content-Type'), 'text/html')
        ) {
            // El script que borra los parámetros
            $script = <<<'JS'
<script>
  window.addEventListener('load', () => {
    history.replaceState(null, document.title, window.location.pathname);
  });
</script>
JS;
            // Inyectarlo antes de </body>
            $content = str_ireplace('</body>', $script."</body>", $response->getContent());
            $response->setContent($content);
        }

        return $response;
    }
}
