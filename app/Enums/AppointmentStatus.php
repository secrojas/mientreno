<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case Scheduled = 'scheduled';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Missed = 'missed';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'Programado',
            self::Completed => 'Realizado',
            self::Cancelled => 'Cancelado',
            self::Missed => 'No asistí',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Scheduled => 'text-blue-400 bg-blue-400/10 border-blue-400/30',
            self::Completed => 'text-accent-secondary bg-accent-secondary/10 border-accent-secondary/30',
            self::Cancelled => 'text-text-muted bg-white/5 border-white/10',
            self::Missed => 'text-red-400 bg-red-400/10 border-red-400/30',
        };
    }
}
