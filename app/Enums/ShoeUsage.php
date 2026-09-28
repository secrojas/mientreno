<?php

namespace App\Enums;

enum ShoeUsage: string
{
    case Daily = 'daily';
    case Tempo = 'tempo';
    case LongRun = 'long_run';
    case Race = 'race';
    case Trail = 'trail';

    public function label(): string
    {
        return match ($this) {
            self::Daily => 'Entrenamiento diario',
            self::Tempo => 'Series y tempo',
            self::LongRun => 'Fondos largos',
            self::Race => 'Competencia',
            self::Trail => 'Trail',
        };
    }

    /**
     * Vida útil orientativa en km cuando el modelo no está en el catálogo.
     */
    public function defaultMaxKm(): int
    {
        return match ($this) {
            self::Daily, self::LongRun => 700,
            self::Tempo => 600,
            self::Race => 400,
            self::Trail => 650,
        };
    }
}
