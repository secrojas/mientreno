<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMedicalAppointmentRequest extends FormRequest
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
            'doctor_id' => [
                'nullable',
                Rule::exists('doctors', 'id')->where('user_id', $this->user()->id),
            ],
            'new_doctor_name' => ['nullable', 'string', 'max:255'],
            'new_doctor_specialty' => ['nullable', 'string', 'max:255'],
            'scheduled_at' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'reason' => ['nullable', 'string', 'max:255'],
            'referred_from_id' => [
                'nullable',
                Rule::exists('medical_appointments', 'id')->where('user_id', $this->user()->id),
            ],
            'referral_task_id' => [
                'nullable',
                Rule::exists('medical_appointment_tasks', 'id')->whereIn(
                    'medical_appointment_id',
                    $this->user()->medicalAppointments()->pluck('id')->all(),
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'scheduled_at.required' => 'Ingresá la fecha y hora del turno.',
            'scheduled_at.date' => 'La fecha del turno no es válida.',
        ];
    }
}
