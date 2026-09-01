<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMedicalDocumentGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'doctor_id' => [
                'nullable',
                Rule::exists('doctors', 'id')->where('user_id', $this->user()->id),
            ],
            'document_ids' => ['required', 'array', 'min:1'],
            'document_ids.*' => [
                Rule::exists('medical_documents', 'id')->where('user_id', $this->user()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Ingresá un título para el reporte de estudios.',
            'document_ids.required' => 'Seleccioná al menos un estudio.',
            'document_ids.min' => 'Seleccioná al menos un estudio.',
        ];
    }
}
