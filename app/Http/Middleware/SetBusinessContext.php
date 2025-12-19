<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Business;
use Symfony\Component\HttpFoundation\Response;

class SetBusinessContext
{
    /**
     * Handle an incoming request.
     *
     * Establece el contexto de business para la request actual.
     * Si hay slug en la ruta, lo busca y lo comparte con las vistas.
     * Si el usuario tiene business pero accede sin prefijo, lo comparte también.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $business = null;

        // Si hay slug de business en la ruta
        if ($slug = $request->route('business')) {
            $business = Business::where('slug', $slug)
                ->where('is_active', true)
                ->firstOrFail();

            // Compartir con todas las vistas
            View::share('currentBusiness', $business);

            // Guardar en request para acceso en controllers
            $request->attributes->set('business', $business);
        }

        // Si usuario autenticado tiene business pero accede sin prefijo
        if (!$business && auth()->check() && auth()->user()->business_id) {
            $business = auth()->user()->business;
            if ($business && $business->is_active) {
                View::share('currentBusiness', $business);
                $request->attributes->set('business', $business);
            }
        }

        return $next($request);
    }
}
