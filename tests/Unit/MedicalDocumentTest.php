<?php

namespace Tests\Unit;

use App\Models\MedicalDocument;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\After;
use Tests\TestCase;

class MedicalDocumentTest extends TestCase
{
    #[After]
    public function resetTestNow(): void
    {
        Carbon::setTestNow();
    }

    public function test_is_expired_when_expiry_date_is_in_the_past(): void
    {
        Carbon::setTestNow('2026-09-01');

        $document = MedicalDocument::factory()->make(['expires_at' => '2026-08-01']);

        $this->assertTrue($document->isExpired());
    }

    public function test_is_not_expired_when_expiry_date_is_in_the_future(): void
    {
        Carbon::setTestNow('2026-09-01');

        $document = MedicalDocument::factory()->make(['expires_at' => '2027-06-11']);

        $this->assertFalse($document->isExpired());
    }

    public function test_is_expiring_soon_within_thirty_days(): void
    {
        Carbon::setTestNow('2026-09-01');

        $document = MedicalDocument::factory()->make(['expires_at' => '2026-09-20']);

        $this->assertTrue($document->isExpiringSoon());
    }

    public function test_is_not_expiring_soon_far_in_the_future(): void
    {
        Carbon::setTestNow('2026-09-01');

        // Regresión: un certificado que vence en casi un año (2026-06-11 a 2027-06-11)
        // se marcaba incorrectamente como "por vencer" por el cambio de comportamiento
        // de Carbon::diffInDays() en Carbon 3 (ya no es absoluto por defecto).
        $document = MedicalDocument::factory()->make(['expires_at' => '2027-06-11']);

        $this->assertFalse($document->isExpiringSoon());
    }

    public function test_is_not_expiring_soon_when_already_expired(): void
    {
        Carbon::setTestNow('2026-09-01');

        $document = MedicalDocument::factory()->make(['expires_at' => '2026-08-01']);

        $this->assertFalse($document->isExpiringSoon());
    }

    public function test_is_not_expiring_soon_without_expiry_date(): void
    {
        $document = MedicalDocument::factory()->make(['expires_at' => null]);

        $this->assertFalse($document->isExpiringSoon());
    }

    public function test_days_until_expiry_is_positive_for_future_dates(): void
    {
        Carbon::setTestNow('2026-09-01');

        $document = MedicalDocument::factory()->make(['expires_at' => '2026-09-11']);

        $this->assertSame(10, $document->days_until_expiry);
    }
}
