<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NOM_MIDDLEWARE
{
    public function handle(Request $request, Closure $next): Response
    {
        // Laisse passer le rôle 'ROLE' et l'admin, bloque les autres (403)
        if (!auth()->check() || !in_array(auth()->user()->role, ['ROLE', 'admin'])) {
            abort(403, 'Accès interdit');
        }

        return $next($request);
    }
}
