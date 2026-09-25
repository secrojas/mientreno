<?php

namespace App\Enums;

use Illuminate\Support\Carbon;

enum TrainingReportPeriod: int
{
    case LastMonth = 1;
    case LastThreeMonths = 3;
    case LastSixMonths = 6;

    public function label(): string
    {
        return match ($this) {
            self::LastMonth => 'Último mes',
            self::LastThreeMonths => 'Últimos 3 meses',
            self::LastSixMonths => 'Últimos 6 meses',
        };
    }

    public function startDate(): Carbon
    {
        return now()->subMonthsNoOverflow($this->value)->startOfDay();
    }
}
