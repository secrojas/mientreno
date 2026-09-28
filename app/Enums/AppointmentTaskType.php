<?php

namespace App\Enums;

enum AppointmentTaskType: string
{
    case Medication = 'medication';
    case Referral = 'referral';
    case Study = 'study';
    case Control = 'control';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Medication => 'Medicación',
            self::Referral => 'Derivación',
            self::Study => 'Estudio a realizar',
            self::Control => 'Control',
            self::Other => 'Otro',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Medication => '💊',
            self::Referral => '👨‍⚕️',
            self::Study => '🔬',
            self::Control => '📅',
            self::Other => '📝',
        };
    }

    public function placeholder(): string
    {
        return match ($this) {
            self::Medication => 'ej: Atorvastatina 10 mg, 1 por noche',
            self::Referral => 'ej: Consultar cirujano por pólipo vesicular',
            self::Study => 'ej: Perfil lipídico en 3 meses',
            self::Control => 'ej: Volver con resultados',
            self::Other => 'ej: Bajar el consumo de grasas',
        };
    }
}
