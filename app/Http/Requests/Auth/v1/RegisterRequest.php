<?php

namespace App\Http\Requests\Auth\v1;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'name' => ['required','string','max:120'],
            'email' => ['required','string','email','max:255'],
            'password' => ['required','string','min:8','confirmed'],
        ];
    }
}