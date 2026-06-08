<?php

namespace Tests\Feature;

use App\Models\ReportShare;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_medical_report(): void
    {
        $user = User::factory()->create();
        Workout::factory()->count(3)->completed()->for($user)->create();

        $response = $this->actingAs($user)->get(route('medical.report'));

        $response->assertOk();
        $response->assertViewIs('medical.report');
        $response->assertViewHas('report');
    }

    public function test_medical_report_contains_required_keys(): void
    {
        $user = User::factory()->create();
        Workout::factory()->count(5)->completed()->for($user)->create();

        $response = $this->actingAs($user)->get(route('medical.report'));

        $report = $response->viewData('report');
        $this->assertArrayHasKey('global_summary', $report);
        $this->assertArrayHasKey('monthly_breakdown', $report);
        $this->assertArrayHasKey('top_workouts', $report);
        $this->assertArrayHasKey('distribution', $report);
    }

    public function test_top_workouts_limited_to_ten(): void
    {
        $user = User::factory()->create();
        Workout::factory()->count(15)->completed()->for($user)->create();

        $response = $this->actingAs($user)->get(route('medical.report'));

        $report = $response->viewData('report');
        $this->assertLessThanOrEqual(10, $report['top_workouts']->count());
    }

    public function test_top_workouts_sorted_by_distance_descending(): void
    {
        $user = User::factory()->create();
        Workout::factory()->completed()->for($user)->create(['distance' => 5.0, 'date' => now()->subDays(1)]);
        Workout::factory()->completed()->for($user)->create(['distance' => 25.0, 'date' => now()->subDays(2)]);
        Workout::factory()->completed()->for($user)->create(['distance' => 15.0, 'date' => now()->subDays(3)]);

        $response = $this->actingAs($user)->get(route('medical.report'));

        $top = $response->viewData('report')['top_workouts'];
        $this->assertEquals(25.0, (float) $top->first()->distance);
    }

    public function test_monthly_breakdown_groups_by_year(): void
    {
        $user = User::factory()->create();
        Workout::factory()->completed()->for($user)->create(['date' => '2025-03-10', 'distance' => 10.0]);
        Workout::factory()->completed()->for($user)->create(['date' => '2026-01-15', 'distance' => 12.0]);

        $response = $this->actingAs($user)->get(route('medical.report'));

        $monthly = $response->viewData('report')['monthly_breakdown'];
        $this->assertArrayHasKey('2025', $monthly);
        $this->assertArrayHasKey('2026', $monthly);
    }

    public function test_only_completed_workouts_counted(): void
    {
        $user = User::factory()->create();
        Workout::factory()->completed()->for($user)->create(['distance' => 10.0, 'date' => now()]);
        Workout::factory()->planned()->for($user)->create(['distance' => 99.0, 'date' => now()]);
        Workout::factory()->skipped()->for($user)->create(['distance' => 99.0, 'date' => now()]);

        $response = $this->actingAs($user)->get(route('medical.report'));

        $summary = $response->viewData('report')['global_summary'];
        $this->assertEquals(1, $summary['total_sessions']);
    }

    public function test_authenticated_user_can_download_medical_pdf(): void
    {
        $user = User::factory()->create();
        Workout::factory()->count(2)->completed()->for($user)->create();

        $response = $this->actingAs($user)->get(route('medical.report.pdf'));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_user_can_generate_medical_share_link(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('medical.report.share'));

        $response->assertOk();
        $response->assertJsonStructure(['success', 'url', 'expires_at']);
        $response->assertJson(['success' => true]);
    }

    public function test_medical_share_valid_for_seven_days(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson(route('medical.report.share'));

        $share = ReportShare::where('user_id', $user->id)->where('report_type', 'medical')->first();
        $this->assertNotNull($share);
        $this->assertTrue($share->expires_at->greaterThan(now()->addDays(6)));
    }

    public function test_shared_medical_report_accessible_without_auth(): void
    {
        $user = User::factory()->create();
        Workout::factory()->count(3)->completed()->for($user)->create();

        $share = ReportShare::createShare(
            userId: $user->id,
            reportType: 'medical',
            year: 0,
            period: 0,
            hoursValid: 168,
        );

        $response = $this->get(route('reports.shared', $share->token));

        $response->assertOk();
        $response->assertViewIs('medical.public.report');
    }

    public function test_expired_medical_share_returns_404(): void
    {
        $user = User::factory()->create();

        $share = ReportShare::create([
            'user_id' => $user->id,
            'report_type' => 'medical',
            'year' => 0,
            'period' => 0,
            'token' => ReportShare::generateToken(),
            'expires_at' => now()->subHour(),
        ]);

        $response = $this->get(route('reports.shared', $share->token));

        $response->assertNotFound();
    }

    public function test_guest_cannot_access_medical_report(): void
    {
        $response = $this->get(route('medical.report'));

        $response->assertRedirect(route('login'));
    }
}
