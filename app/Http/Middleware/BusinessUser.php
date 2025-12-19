<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BusinessUser
{
    /**
     * Handle an incoming request.
     *
     * Permite acceso solo a usuarios CON business.
     * Valida que el business en la URL coincida con el business del usuario.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $businessSlug = $request->route('business');

        // Si el usuario no tiene business, redirigir a rutas individuales
        if (!$user->business_id) {
            return redirect()->route('dashboard');
        }

        // Validar que el business en la URL sea el del usuario
        if ($businessSlug && $user->business->slug !== $businessSlug) {
            abort(403, 'No tienes acceso a este business.');
        }

        return $next($request);
    }
}
