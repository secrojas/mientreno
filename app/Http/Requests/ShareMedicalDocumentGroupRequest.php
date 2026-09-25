<?php

namespace App\Http\Requests;

use App\Enums\TrainingReportPeriod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ShareMedicalDocumentGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('group')->user_id === $this->user()->id;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'include_training' => ['sometimes', 'boolean'],
            'training_period_months' => [
                'required_if_accepted:include_training',
                'nullable',
                new Enum(TrainingReportPeriod::class),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'training_period_months.required_if_accepted' => 'Elegí el período de entrenamientos a incluir.',
            'training_period_months.Illuminate\Validation\Rules\Enum' => 'El período de entrenamientos no es válido.',
        ];
    }

    public function trainingPeriod(): ?TrainingReportPeriod
    {
        if (! $this->boolean('include_training')) {
            return null;
        }

        return TrainingReportPeriod::from((int) $this->input('training_period_months'));
    }
}
