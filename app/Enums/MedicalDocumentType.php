<?php

namespace App\Enums;

enum MedicalDocumentType: string
{
    case BloodTest = 'blood_test';
    case Ergometry = 'ergometry';
    case Ecg = 'ecg';
    case Echocardiogram = 'echocardiogram';
    case FitnessCertificate = 'fitness_certificate';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::BloodTest => 'Análisis de Sangre',
            self::Ergometry => 'Ergometría',
            self::Ecg => 'Electrocardiograma',
            self::Echocardiogram => 'Ecocardiograma Doppler',
            self::FitnessCertificate => 'Apto Médico',
            self::Other => 'Otro',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::BloodTest => 'text-red-400 bg-red-400/10 border-red-400/30',
            self::Ergometry => 'text-orange-400 bg-orange-400/10 border-orange-400/30',
            self::Ecg => 'text-blue-400 bg-blue-400/10 border-blue-400/30',
            self::Echocardiogram => 'text-purple-400 bg-purple-400/10 border-purple-400/30',
            self::FitnessCertificate => 'text-emerald-400 bg-emerald-400/10 border-emerald-400/30',
            self::Other => 'text-gray-400 bg-gray-400/10 border-gray-400/30',
        };
    }
}
