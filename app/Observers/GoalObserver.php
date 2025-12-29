<?php

namespace App\Observers;

use App\Models\Goal;
use Illuminate\Support\Facades\Cache;

class GoalObserver
{
    /**
     * Handle the Goal "created" event.
     */
    public function created(Goal $goal): void
    {
        $this->clearDashboardCache($goal->user_id);
    }

    /**
     * Handle the Goal "updated" event.
     */
    public function updated(Goal $goal): void
    {
        $this->clearDashboardCache($goal->user_id);
    }

    /**
     * Handle the Goal "deleted" event.
     */
    public function deleted(Goal $goal): void
    {
        $this->clearDashboardCache($goal->user_id);
    }

    /**
     * Handle the Goal "restored" event.
     */
    public function restored(Goal $goal): void
    {
        $this->clearDashboardCache($goal->user_id);
    }

    /**
     * Handle the Goal "force deleted" event.
     */
    public function forceDeleted(Goal $goal): void
    {
        $this->clearDashboardCache($goal->user_id);
    }

    /**
     * Clear dashboard cache for the user
     */
    protected function clearDashboardCache(int $userId): void
    {
        $cacheKey = "dashboard_data_user_{$userId}_week_" . now()->weekOfYear();
        Cache::forget($cacheKey);
    }
}
