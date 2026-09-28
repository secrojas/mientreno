<?php

namespace App\Http\Requests;

use App\Enums\AppointmentTaskType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreMedicalAppointmentTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('appointment')->user_id === $this->user()->id;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', new Enum(AppointmentTaskType::class)],
            'description' => ['required', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Elegí el tipo de indicación.',
            'description.required' => 'Describí la indicación.',
        ];
    }
}
