<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// BASE LARAVEL + PROYECTO:
// Middleware de Laravel creado para este proyecto.
// Se ejecuta antes de entrar a rutas admin para filtrar acceso.
class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // PROYECTO:
        // Regla de negocio:
        // - Debe haber usuario autenticado
        // - Debe ser admin (metodo isAdmin() del modelo User)
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403);
        }

        // BASE LARAVEL:
        // Si pasa la validacion, la peticion continua.
        return $next($request);
    }
}
