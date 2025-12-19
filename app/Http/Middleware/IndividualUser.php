<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IndividualUser
{
    /**
     * Handle an incoming request.
     *
     * Permite acceso solo a usuarios SIN business (individuales).
     * Si el usuario tiene business, redirige a la ruta con prefijo business.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Si el usuario tiene business, redirigir a ruta con prefijo
        if ($user && $user->business_id && $user->business) {
            $business = $user->business;
            $currentRoute = $request->route()->getName();

            // Generar ruta equivalente con prefijo business
            if ($currentRoute) {
                $businessRoute = str_replace('.', '.', $currentRoute);
                return redirect()->route($businessRoute, ['business' => $business->slug]);
            }

            // Fallback: redirigir a dashboard del business
            return redirect()->route('dashboard', ['business' => $business->slug]);
        }

        return $next($request);
    }
}
