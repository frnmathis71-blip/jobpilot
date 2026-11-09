<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware IsCompany : réserve une route aux entreprises (rôle "company").
 *
 * Il est enregistré sous l'alias 'IsCompany' dans bootstrap/app.php,
 * puis utilisé dans routes/web.php : Route::middleware(['auth', 'IsCompany']).
 */
class IsCompany
{
    /**
     * Exécuté avant le contrôleur, à chaque requête sur une route protégée.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // On bloque si l'utilisateur n'est pas connecté
        // ou si son rôle (colonne users.role) n'est pas "company".
        if (!auth()->check() || auth()->user()->role!=='company'){
            abort(403, 'Accès interdit');
        }

        // Sinon, on laisse passer la requête vers le contrôleur.
        return $next($request);
    }
}
