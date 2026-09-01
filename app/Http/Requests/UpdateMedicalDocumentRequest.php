<?php

namespace App\Http\Requests;

use App\Enums\MedicalDocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateMedicalDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->route('document')->user_id === $this->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(MedicalDocumentType::class)],
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'document' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
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
            'document.mimes' => 'Solo se aceptan archivos PDF.',
            'document.max' => 'El archivo no puede superar 10MB.',
        ];
    }
}
