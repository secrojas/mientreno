<?php

namespace App\Http\Controllers\Auth\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\v1\RegisterRequest;
use App\Models\User;
use App\Support\BusinessContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm(BusinessContext $ctx)
    {
        return view('auth.register', ['biz' => $ctx->get()]);
    }

    public function register(RegisterRequest $request, BusinessContext $ctx)
    {
        $data = $request->validated();
        $user = User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'business_id' => $ctx->id(),
            'role'        => 'user',
        ]);

        Auth::login($user);
        return redirect()->route('biz.dashboard', ['business' => $ctx->get()->slug]);
    }
}
