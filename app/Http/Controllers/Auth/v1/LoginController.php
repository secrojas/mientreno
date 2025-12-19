<?php

namespace App\Http\Controllers\Auth\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\v1\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        // Buscar usuario por email (puede tener o no business)
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Credenciales inválidas.']);
        }

        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        // Redirección inteligente por rol y contexto de business
        return redirect()->intended($this->redirectPath($user));
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
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
