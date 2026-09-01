<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMedicalOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('order')->user_id === $this->user()->id;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'issued_at' => ['nullable', 'date'],
            'doctor_id' => [
                'nullable',
                Rule::exists('doctors', 'id')->where('user_id', $this->user()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Ingresá un título para la orden.',
            'file.mimes' => 'Solo se aceptan imágenes (JPG, PNG) o PDF.',
            'file.max' => 'El archivo no puede superar 10MB.',
        ];
    }
}
