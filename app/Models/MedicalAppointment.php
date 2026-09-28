<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalAppointment extends Model
{
    /** @use HasFactory<\Database\Factories\MedicalAppointmentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'doctor_id',
        'referred_from_id',
        'scheduled_at',
        'location',
        'reason',
        'status',
        'observations',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'status' => AppointmentStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function referredFrom(): BelongsTo
    {
        return $this->belongsTo(self::class, 'referred_from_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(self::class, 'referred_from_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(MedicalAppointmentTask::class);
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(MedicalDocument::class, 'medical_appointment_documents')
            ->withTimestamps();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(MedicalOrder::class);
    }

    /**
     * @param  Builder<MedicalAppointment>  $query
     */
    public function scopeUpcoming(Builder $query): void
    {
        $query->where('status', AppointmentStatus::Scheduled)
            ->where('scheduled_at', '>=', now());
    }

    /**
     * @param  Builder<MedicalAppointment>  $query
     */
    public function scopeAwaitingFollowUp(Builder $query): void
    {
        $query->where('status', AppointmentStatus::Scheduled)
            ->where('scheduled_at', '<', now());
    }

    /**
     * Turno programado cuya fecha ya pasó: hay que registrar cómo fue.
     */
    public function isAwaitingFollowUp(): bool
    {
        return $this->status === AppointmentStatus::Scheduled && $this->scheduled_at->isPast();
    }

    public function title(): string
    {
        if ($this->doctor) {
            return $this->doctor->name.($this->doctor->specialty ? ' — '.$this->doctor->specialty : '');
        }

        return $this->reason ?: 'Turno médico';
    }
}
