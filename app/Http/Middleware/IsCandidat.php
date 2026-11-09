<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware IsCandidat : réserve une route aux candidats (rôle "candidat").
 *
 * Il est enregistré sous l'alias 'IsCandidat' dans bootstrap/app.php,
 * puis utilisé dans routes/web.php : Route::middleware(['auth', 'IsCandidat']).
 */
class IsCandidat
{
    /**
     * Exécuté avant le contrôleur, à chaque requête sur une route protégée.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // On bloque si l'utilisateur n'est pas connecté
        // ou si son rôle (colonne users.role) n'est pas "candidat".
        if (!auth()->check() || auth()->user()->role!=='candidat'){
            abort(403, 'Accès interdit');
        }

        // Sinon, on laisse passer la requête vers le contrôleur.
        return $next($request);
    }
}
