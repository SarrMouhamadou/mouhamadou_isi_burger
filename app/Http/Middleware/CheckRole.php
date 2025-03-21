<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Vérifie si l'utilisateur est authentifié et si son rôle correspond
        if (!$request->user() || !$request->user()->role || $request->user()->role->name !== $role) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}
