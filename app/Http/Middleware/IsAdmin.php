<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware IsAdmin : réserve une route aux administrateurs (rôle "admin").
 *
 * Il est enregistré sous l'alias 'IsAdmin' dans bootstrap/app.php,
 * puis utilisé dans routes/web.php : Route::middleware(['auth', 'IsAdmin']).
 */
class IsAdmin
{
    /**
     * Exécuté avant le contrôleur, à chaque requête sur une route protégée.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // On bloque si l'utilisateur n'est pas connecté
        // ou si son rôle (colonne users.role) n'est pas "admin".
        if (!auth()->check() || auth()->user()->role!=='admin'){
            abort(403, 'Accès interdit');
        }

        // Sinon, on laisse passer la requête vers le contrôleur.
        return $next($request);
    }
}
