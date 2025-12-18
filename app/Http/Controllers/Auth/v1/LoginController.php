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

        // Redirección diferenciada por rol
        if ($user->role === 'coach' || $user->role === 'admin') {
            return redirect()->intended(route('coach.dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }
}
