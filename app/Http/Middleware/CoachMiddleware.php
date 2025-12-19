<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CoachMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Permite acceso solo a usuarios con rol coach o admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user || !in_array($user->role, ['coach', 'admin'])) {
            abort(403, 'Solo coaches y administradores pueden acceder a esta sección.');
        }

        return $next($request);
    }
}
