<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Workout;
use App\Services\MetricsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MetricsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MetricsService $metricsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
        $this->metricsService = new MetricsService();
    }

    public function test_get_weekly_metrics_returns_correct_data()
    {
        $user = User::factory()->create();

        // Crear workouts de esta semana (completados)
        Workout::factory()->count(3)->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status' => 'completed',
            'distance' => 10,
            'duration' => 3600,
        ]);

        // Crear un workout de la semana pasada (no debe contarse)
        Workout::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->subWeek(),
            'status' => 'completed',
            'distance' => 5,
        ]);

        // Crear un workout planned de esta semana (no debe contarse)
        Workout::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status' => 'planned',
            'distance' => 8,
        ]);

        $metrics = $this->metricsService->getWeeklyMetrics($user);

        $this->assertEquals(30, $metrics['total_distance']); // 3 workouts x 10km
        $this->assertEquals(10800, $metrics['total_duration']); // 3 workouts x 3600s
        $this->assertEquals(3, $metrics['total_workouts']);
    }

    public function test_get_monthly_metrics_returns_correct_data()
    {
        $user = User::factory()->create();

        // Crear workouts de este mes
        Workout::factory()->count(5)->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status' => 'completed',
            'distance' => 8,
            'duration' => 2400,
        ]);

        // Crear workout del mes pasado (no debe contarse)
        Workout::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->subMonth(),
            'status' => 'completed',
            'distance' => 10,
        ]);

        $metrics = $this->metricsService->getMonthlyMetrics($user);

        $this->assertEquals(40, $metrics['total_distance']); // 5 workouts x 8km
        $this->assertEquals(12000, $metrics['total_duration']); // 5 workouts x 2400s
        $this->assertEquals(5, $metrics['total_workouts']);
    }

    public function test_get_total_metrics_counts_all_completed_workouts()
    {
        $user = User::factory()->create();

        // Crear workouts completados en diferentes fechas
        Workout::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status' => 'completed',
            'distance' => 10,
        ]);

        Workout::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->subMonths(3),
            'status' => 'completed',
            'distance' => 15,
        ]);

        Workout::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->subYear(),
            'status' => 'completed',
            'distance' => 20,
        ]);

        // Crear workout skipped (no debe contarse)
        Workout::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status' => 'skipped',
            'distance' => 5,
        ]);

        $metrics = $this->metricsService->getTotalMetrics($user);

        $this->assertEquals(45, $metrics['total_distance']); // 10 + 15 + 20
        $this->assertEquals(3, $metrics['total_workouts']);
    }

    public function test_metrics_only_count_completed_workouts()
    {
        $user = User::factory()->create();

        Workout::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status' => 'completed',
            'distance' => 10,
        ]);

        Workout::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status' => 'planned',
            'distance' => 8,
        ]);

        Workout::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status' => 'skipped',
            'distance' => 12,
        ]);

        $metrics = $this->metricsService->getWeeklyMetrics($user);

        $this->assertEquals(10, $metrics['total_distance']);
        $this->assertEquals(1, $metrics['total_workouts']);
    }

    public function test_format_duration_returns_correct_format()
    {
        // Menos de 1 hora
        $this->assertEquals('45m', $this->metricsService->formatDuration(2700)); // 45 minutos

        // Exactamente 1 hora
        $this->assertEquals('1h 0m', $this->metricsService->formatDuration(3600));

        // Más de 1 hora
        $this->assertEquals('1h 30m', $this->metricsService->formatDuration(5400)); // 1h 30m

        // Múltiples horas
        $this->assertEquals('2h 15m', $this->metricsService->formatDuration(8100)); // 2h 15m
    }

    public function test_format_pace_returns_correct_format()
    {
        // Pace normal
        $this->assertEquals('5:00', $this->metricsService->formatPace(300)); // 5 min/km

        // Pace rápido
        $this->assertEquals('4:30', $this->metricsService->formatPace(270)); // 4:30 min/km

        // Pace lento
        $this->assertEquals('6:15', $this->metricsService->formatPace(375)); // 6:15 min/km

        // Null
        $this->assertEquals('–', $this->metricsService->formatPace(null));
    }

    public function test_get_workout_type_distribution()
    {
        $user = User::factory()->create();

        Workout::factory()->count(3)->create([
            'user_id' => $user->id,
            'type' => 'easy_run',
            'status' => 'completed',
            'distance' => 10,
        ]);

        Workout::factory()->count(2)->create([
            'user_id' => $user->id,
            'type' => 'long_run',
            'status' => 'completed',
            'distance' => 20,
        ]);

        Workout::factory()->create([
            'user_id' => $user->id,
            'type' => 'intervals',
            'status' => 'completed',
            'distance' => 8,
        ]);

        $distribution = $this->metricsService->getWorkoutTypeDistribution($user);

        $this->assertEquals(3, $distribution['easy_run']['count']);
        $this->assertEquals(30, $distribution['easy_run']['total_distance']);

        $this->assertEquals(2, $distribution['long_run']['count']);
        $this->assertEquals(40, $distribution['long_run']['total_distance']);

        $this->assertEquals(1, $distribution['intervals']['count']);
        $this->assertEquals(8, $distribution['intervals']['total_distance']);
    }

    public function test_calculate_streak_with_consecutive_days()
    {
        $user = User::factory()->create();

        // Racha de 3 días consecutivos (hoy, ayer, anteayer)
        Workout::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status' => 'completed',
        ]);

        Workout::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::yesterday(),
            'status' => 'completed',
        ]);

        Workout::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->subDays(2),
            'status' => 'completed',
        ]);

        // Workout hace 5 días (rompe la racha - gap de 2 días)
        Workout::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->subDays(5),
            'status' => 'completed',
        ]);

        $streak = $this->metricsService->calculateStreak($user);

        $this->assertEquals(3, $streak);
    }

    public function test_calculate_streak_returns_zero_when_no_recent_workouts()
    {
        $user = User::factory()->create();

        // Workout hace 3 días (no es consecutivo desde hoy)
        Workout::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->subDays(3),
            'status' => 'completed',
        ]);

        $streak = $this->metricsService->calculateStreak($user);

        $this->assertEquals(0, $streak);
    }

    public function test_metrics_are_isolated_per_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // User 1 tiene 3 workouts
        Workout::factory()->count(3)->create([
            'user_id' => $user1->id,
            'date' => Carbon::today(),
            'status' => 'completed',
            'distance' => 10,
        ]);

        // User 2 tiene 5 workouts
        Workout::factory()->count(5)->create([
            'user_id' => $user2->id,
            'date' => Carbon::today(),
            'status' => 'completed',
            'distance' => 8,
        ]);

        $metrics1 = $this->metricsService->getWeeklyMetrics($user1);
        $metrics2 = $this->metricsService->getWeeklyMetrics($user2);

        $this->assertEquals(3, $metrics1['total_workouts']);
        $this->assertEquals(30, $metrics1['total_distance']);

        $this->assertEquals(5, $metrics2['total_workouts']);
        $this->assertEquals(40, $metrics2['total_distance']);
    }
}
