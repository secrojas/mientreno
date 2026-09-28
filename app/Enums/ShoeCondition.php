<?php

namespace App\Enums;

enum ShoeCondition: string
{
    case Good = 'good';
    case NearLimit = 'near_limit';
    case Worn = 'worn';
    case Retired = 'retired';

    /**
     * Porcentaje de vida útil a partir del cual el par está cerca del límite.
     */
    public const NEAR_LIMIT_RATIO = 0.8;

    public static function fromUsage(float $usedRatio, bool $isRetired): self
    {
        return match (true) {
            $isRetired => self::Retired,
            $usedRatio >= 1 => self::Worn,
            $usedRatio >= self::NEAR_LIMIT_RATIO => self::NearLimit,
            default => self::Good,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Good => 'En forma',
            self::NearLimit => 'Cerca del límite',
            self::Worn => 'Para retirar',
            self::Retired => 'Retirada',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Good => 'text-accent-secondary bg-accent-secondary/10 border-accent-secondary/30',
            self::NearLimit => 'text-amber-400 bg-amber-400/10 border-amber-400/30',
            self::Worn => 'text-accent-primary bg-accent-primary/10 border-accent-primary/30',
            self::Retired => 'text-text-muted bg-white/5 border-white/10',
        };
    }

    public function ringColor(): string
    {
        return match ($this) {
            self::Good => '#2DE38E',
            self::NearLimit => '#F59E0B',
            self::Worn => '#FF3B5C',
            self::Retired => '#4B5563',
        };
    }
}
