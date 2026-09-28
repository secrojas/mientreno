<?php

namespace App\Http\Requests;

use App\Enums\AppointmentStatus;
use App\Enums\AppointmentTaskType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateMedicalAppointmentRequest extends FormRequest
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
            'doctor_id' => [
                'nullable',
                Rule::exists('doctors', 'id')->where('user_id', $this->user()->id),
            ],
            'scheduled_at' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'reason' => ['nullable', 'string', 'max:255'],
            'status' => ['required', new Enum(AppointmentStatus::class)],
            'observations' => ['nullable', 'string', 'max:10000'],
            'document_ids' => ['nullable', 'array'],
            'document_ids.*' => [
                Rule::exists('medical_documents', 'id')->where('user_id', $this->user()->id),
            ],
            'order_ids' => ['nullable', 'array'],
            'order_ids.*' => [
                Rule::exists('medical_orders', 'id')->where('user_id', $this->user()->id),
            ],
            'new_tasks' => ['nullable', 'array'],
            'new_tasks.*.type' => ['required_with:new_tasks.*.description', new Enum(AppointmentTaskType::class)],
            'new_tasks.*.description' => ['nullable', 'string', 'max:255'],
            'new_tasks.*.due_date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'scheduled_at.required' => 'Ingresá la fecha y hora del turno.',
            'status.required' => 'Elegí el estado del turno.',
            'observations.max' => 'Las observaciones no pueden superar los 10.000 caracteres.',
        ];
    }

    /**
     * Indicaciones nuevas cargadas desde el formulario, ignorando filas vacías.
     *
     * @return array<int, array{type: string, description: string, due_date: ?string}>
     */
    public function newTasks(): array
    {
        return collect($this->input('new_tasks', []))
            ->filter(fn (array $task): bool => filled($task['description'] ?? null))
            ->map(fn (array $task): array => [
                'type' => $task['type'],
                'description' => $task['description'],
                'due_date' => ($task['due_date'] ?? null) ?: null,
            ])
            ->values()
            ->all();
    }
}
