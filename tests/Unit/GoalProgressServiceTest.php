<?php

namespace Tests\Unit;

use App\Models\Goal;
use App\Models\Race;
use App\Models\User;
use App\Models\Workout;
use App\Services\GoalProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class GoalProgressServiceTest extends TestCase
{
    use RefreshDatabase;

    protected GoalProgressService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
        $this->service = new GoalProgressService();
    }

    public function test_calculate_race_progress_without_race_id_returns_zero()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->raceGoal()->create([
            'user_id' => $user->id,
            'race_id' => null,
        ]);

        $progress = $this->service->calculateProgress($goal);

        $this->assertEquals(0, $progress['current_value']);
        $this->assertEquals(0, $progress['percentage']);
    }

    public function test_calculate_race_progress_without_race_workout_returns_zero()
    {
        $user = User::factory()->create();
        $race = Race::factory()->create(['user_id' => $user->id]);
        $goal = Goal::factory()->raceGoal()->create([
            'user_id' => $user->id,
            'race_id' => $race->id,
            'target_value' => ['time' => 7200], // 2 horas
        ]);

        $progress = $this->service->calculateProgress($goal);

        $this->assertEquals(0, $progress['current_value']);
        $this->assertEquals(0, $progress['percentage']);
    }

    public function test_calculate_race_progress_when_goal_achieved()
    {
        $user = User::factory()->create();
        $race = Race::factory()->create(['user_id' => $user->id]);
        $goal = Goal::factory()->raceGoal()->create([
            'user_id' => $user->id,
            'race_id' => $race->id,
            'target_value' => ['time' => 7200], // Target: 2 horas
        ]);

        // Crear workout de la carrera con tiempo mejor que el objetivo
        Workout::factory()->create([
            'user_id' => $user->id,
            'race_id' => $race->id,
            'type' => 'race',
            'duration' => 6900, // 1h 55m (mejor que 2h)
            'date' => Carbon::today(),
        ]);

        $progress = $this->service->calculateProgress($goal);

        $this->assertEquals(6900, $progress['current_value']);
        $this->assertEquals(100, $progress['percentage']);
    }

    public function test_calculate_race_progress_when_goal_not_achieved()
    {
        $user = User::factory()->create();
        $race = Race::factory()->create(['user_id' => $user->id]);
        $goal = Goal::factory()->raceGoal()->create([
            'user_id' => $user->id,
            'race_id' => $race->id,
            'target_value' => ['time' => 7200], // Target: 2 horas
        ]);

        // Crear workout de la carrera con tiempo peor que el objetivo
        Workout::factory()->create([
            'user_id' => $user->id,
            'race_id' => $race->id,
            'type' => 'race',
            'duration' => 8000, // 2h 13m (peor que 2h)
            'date' => Carbon::today(),
        ]);

        $progress = $this->service->calculateProgress($goal);

        $this->assertEquals(8000, $progress['current_value']);
        $this->assertEquals(90, $progress['percentage']); // (7200/8000)*100 = 90%
    }

    public function test_calculate_distance_progress_for_weekly_goal()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->distanceGoal()->create([
            'user_id' => $user->id,
            'target_value' => ['distance' => 50, 'period' => 'week'],
        ]);

        // Crear workouts de esta semana
        Workout::factory()->count(3)->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status' => 'completed',
            'distance' => 10,
        ]);

        // Crear workout de la semana pasada (no debe contarse)
        Workout::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->subWeek(),
            'status' => 'completed',
            'distance' => 20,
        ]);

        $progress = $this->service->calculateProgress($goal);

        $this->assertEquals(30, $progress['current_value']); // 3 x 10km
        $this->assertEquals(60, $progress['percentage']); // 30/50 = 60%
    }

    public function test_calculate_distance_progress_for_monthly_goal()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->distanceGoal()->create([
            'user_id' => $user->id,
            'target_value' => ['distance' => 100, 'period' => 'month'],
        ]);

        // Crear workouts de este mes
        Workout::factory()->count(5)->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status' => 'completed',
            'distance' => 15,
        ]);

        $progress = $this->service->calculateProgress($goal);

        $this->assertEquals(75, $progress['current_value']); // 5 x 15km
        $this->assertEquals(75, $progress['percentage']); // 75/100 = 75%
    }

    public function test_calculate_distance_progress_caps_at_100_percent()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->distanceGoal()->create([
            'user_id' => $user->id,
            'target_value' => ['distance' => 50, 'period' => 'week'],
        ]);

        // Crear workouts que superan el objetivo
        Workout::factory()->count(6)->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status' => 'completed',
            'distance' => 10,
        ]);

        $progress = $this->service->calculateProgress($goal);

        $this->assertEquals(60, $progress['current_value']); // 6 x 10km
        $this->assertEquals(100, $progress['percentage']); // Capped at 100
    }

    public function test_calculate_pace_progress_without_workouts_returns_zero()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->paceGoal()->create([
            'user_id' => $user->id,
            'target_value' => ['pace' => 300], // 5:00 min/km
        ]);

        $progress = $this->service->calculateProgress($goal);

        $this->assertEquals(0, $progress['current_value']);
        $this->assertEquals(0, $progress['percentage']);
    }

    public function test_calculate_pace_progress_when_goal_achieved()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->paceGoal()->create([
            'user_id' => $user->id,
            'target_value' => ['pace' => 300], // Target: 5:00 min/km
        ]);

        // Crear 5 workouts con pace mejor que el objetivo
        Workout::factory()->count(5)->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->subDays(rand(0, 10)),
            'status' => 'completed',
            'avg_pace' => 280, // 4:40 min/km (mejor que 5:00)
        ]);

        $progress = $this->service->calculateProgress($goal);

        $this->assertEquals(280, $progress['current_value']);
        $this->assertEquals(100, $progress['percentage']);
    }

    public function test_calculate_pace_progress_when_improving()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->paceGoal()->create([
            'user_id' => $user->id,
            'target_value' => ['pace' => 300], // Target: 5:00 min/km
        ]);

        // Crear 5 workouts con pace peor que el objetivo
        Workout::factory()->count(5)->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->subDays(rand(0, 10)),
            'status' => 'completed',
            'avg_pace' => 330, // 5:30 min/km (peor que 5:00)
        ]);

        $progress = $this->service->calculateProgress($goal);

        $this->assertEquals(330, $progress['current_value']);
        // Formula: (2 - (330/300)) * 100 = (2 - 1.1) * 100 = 90%
        $this->assertEquals(90, $progress['percentage']);
    }

    public function test_calculate_pace_progress_uses_last_5_workouts()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->paceGoal()->create([
            'user_id' => $user->id,
            'target_value' => ['pace' => 300],
        ]);

        // Crear 3 workouts antiguos con pace malo
        Workout::factory()->count(3)->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->subDays(30),
            'status' => 'completed',
            'avg_pace' => 400, // Pace malo
        ]);

        // Crear 5 workouts recientes con pace bueno
        Workout::factory()->count(5)->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->subDays(rand(0, 5)),
            'status' => 'completed',
            'avg_pace' => 280, // Pace bueno
        ]);

        $progress = $this->service->calculateProgress($goal);

        // Debe usar el promedio de los últimos 5 (280), no de los 8
        $this->assertEquals(280, $progress['current_value']);
        $this->assertEquals(100, $progress['percentage']);
    }

    public function test_calculate_frequency_progress_for_weekly_goal()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->frequencyGoal()->create([
            'user_id' => $user->id,
            'target_value' => ['sessions' => 4, 'period' => 'week'],
        ]);

        // Crear 3 workouts esta semana
        Workout::factory()->count(3)->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status' => 'completed',
        ]);

        // Crear workout la semana pasada (no debe contarse)
        Workout::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::today()->subWeek(),
            'status' => 'completed',
        ]);

        $progress = $this->service->calculateProgress($goal);

        $this->assertEquals(3, $progress['current_value']);
        $this->assertEquals(75, $progress['percentage']); // 3/4 = 75%
    }

    public function test_calculate_frequency_progress_for_monthly_goal()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->frequencyGoal()->create([
            'user_id' => $user->id,
            'target_value' => ['sessions' => 12, 'period' => 'month'],
        ]);

        // Crear 10 workouts este mes
        Workout::factory()->count(10)->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status' => 'completed',
        ]);

        $progress = $this->service->calculateProgress($goal);

        $this->assertEquals(10, $progress['current_value']);
        $this->assertEquals(83, $progress['percentage']); // 10/12 = 83.33% -> 83%
    }

    public function test_update_goal_progress_updates_database()
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->distanceGoal()->create([
            'user_id' => $user->id,
            'target_value' => ['distance' => 50, 'period' => 'week'],
            'progress' => ['current_value' => 0, 'percentage' => 0],
        ]);

        Workout::factory()->count(3)->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status' => 'completed',
            'distance' => 10,
        ]);

        $this->service->updateGoalProgress($goal);

        $goal->refresh();

        $this->assertEquals(30, $goal->progress['current_value']);
        $this->assertEquals(60, $goal->progress['percentage']);
    }

    public function test_update_user_goals_progress_updates_all_active_goals()
    {
        $user = User::factory()->create();

        // Crear 2 goals activos
        $goal1 = Goal::factory()->distanceGoal()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'target_value' => ['distance' => 50, 'period' => 'week'],
            'progress' => ['current_value' => 0, 'percentage' => 0],
        ]);

        $goal2 = Goal::factory()->frequencyGoal()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'target_value' => ['sessions' => 4, 'period' => 'week'],
            'progress' => ['current_value' => 0, 'percentage' => 0],
        ]);

        // Crear 1 goal completado (no debe actualizarse)
        $goal3 = Goal::factory()->distanceGoal()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'target_value' => ['distance' => 100, 'period' => 'month'],
            'progress' => ['current_value' => 100, 'percentage' => 100],
        ]);

        // Crear workouts
        Workout::factory()->count(2)->create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status' => 'completed',
            'distance' => 10,
        ]);

        $this->service->updateUserGoalsProgress($user);

        $goal1->refresh();
        $goal2->refresh();
        $goal3->refresh();

        // Goal 1 (distance) debe actualizarse
        $this->assertEquals(20, $goal1->progress['current_value']);
        $this->assertEquals(40, $goal1->progress['percentage']);

        // Goal 2 (frequency) debe actualizarse
        $this->assertEquals(2, $goal2->progress['current_value']);
        $this->assertEquals(50, $goal2->progress['percentage']);

        // Goal 3 (completed) no debe cambiar
        $this->assertEquals(100, $goal3->progress['current_value']);
        $this->assertEquals(100, $goal3->progress['percentage']);
    }

    public function test_goals_are_isolated_per_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $goal1 = Goal::factory()->distanceGoal()->create([
            'user_id' => $user1->id,
            'target_value' => ['distance' => 50, 'period' => 'week'],
        ]);

        $goal2 = Goal::factory()->distanceGoal()->create([
            'user_id' => $user2->id,
            'target_value' => ['distance' => 50, 'period' => 'week'],
        ]);

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
            'distance' => 10,
        ]);

        $progress1 = $this->service->calculateProgress($goal1);
        $progress2 = $this->service->calculateProgress($goal2);

        $this->assertEquals(30, $progress1['current_value']);
        $this->assertEquals(60, $progress1['percentage']);

        $this->assertEquals(50, $progress2['current_value']);
        $this->assertEquals(100, $progress2['percentage']);
    }
}
