<?php

namespace App\Providers;

use App\Models\Goal;
use App\Models\Race;
use App\Models\Workout;
use App\Observers\GoalObserver;
use App\Observers\RaceObserver;
use App\Observers\WorkoutObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Workout::observe(WorkoutObserver::class);
        Race::observe(RaceObserver::class);
        Goal::observe(GoalObserver::class);
    }
}
