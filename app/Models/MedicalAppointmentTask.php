<?php

namespace App\Models;

use App\Enums\AppointmentTaskType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalAppointmentTask extends Model
{
    /** @use HasFactory<\Database\Factories\MedicalAppointmentTaskFactory> */
    use HasFactory;

    protected $fillable = [
        'medical_appointment_id',
        'follow_up_appointment_id',
        'type',
        'description',
        'due_date',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => AppointmentTaskType::class,
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(MedicalAppointment::class, 'medical_appointment_id');
    }

    public function followUpAppointment(): BelongsTo
    {
        return $this->belongsTo(MedicalAppointment::class, 'follow_up_appointment_id');
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function isOverdue(): bool
    {
        return ! $this->isCompleted() && $this->due_date?->isBefore(today());
    }
}
