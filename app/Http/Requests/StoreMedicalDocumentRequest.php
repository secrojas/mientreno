<?php

namespace App\Http\Requests;

use App\Enums\MedicalDocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreMedicalDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(MedicalDocumentType::class)],
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'document' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'images_url' => ['nullable', 'url', 'max:2048'],
            'issued_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'doctor_id' => [
                'nullable',
                Rule::exists('doctors', 'id')->where('user_id', $this->user()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Seleccioná el tipo de documento.',
            'title.required' => 'Ingresá un título para el documento.',
            'document.required' => 'Seleccioná un archivo PDF.',
            'document.mimes' => 'Solo se aceptan archivos PDF.',
            'document.max' => 'El archivo no puede superar 10MB.',
            'images_url.url' => 'Ingresá un link válido.',
        ];
    }
}
