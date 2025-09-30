<?php

namespace App\Http\Controllers\Auth\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\v1\LoginRequest;
use App\Models\User;
use App\Support\BusinessContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm(BusinessContext $ctx)
    {
        return view('auth.login', ['biz' => $ctx->get()]);
    }

    public function login(LoginRequest $request, BusinessContext $ctx)
    {
        $user = User::where('email', $request->email)
            ->where('business_id', $ctx->id())
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Credenciales inválidas para esta actividad.']);
        }

        Auth::login($user, $request->boolean('remember'));
        return redirect()->route('biz.dashboard', ['business' => $ctx->get()->slug]);
    }

    public function logout(BusinessContext $ctx)
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('biz.login', ['business' => $ctx->get()->slug]);
    }
}
