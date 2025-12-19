<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Redirección inteligente por rol y contexto de business
        $user = Auth::user();

        return redirect()->intended($this->redirectPath($user));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Determina la ruta de redirección según el tipo de usuario y contexto.
     */
    protected function redirectPath(User $user): string
    {
        $business = $user->business;

        // Coaches y Admins
        if (in_array($user->role, ['coach', 'admin'])) {
            // Si no tiene business, redirigir a crear uno
            if (!$business) {
                return route('coach.business.create');
            }

            // Si tiene business, ir a coach dashboard con contexto
            return route('coach.dashboard', ['business' => $business->slug]);
        }

        // Runners
        if ($business) {
            // Usuario con business: ruta con prefijo
            return route('dashboard', ['business' => $business->slug]);
        }

        // Usuario individual: ruta sin prefijo
        return route('dashboard');
    }
}
