<?php

namespace App\Observers;

use App\Models\Race;
use Illuminate\Support\Facades\Cache;

class RaceObserver
{
    /**
     * Handle the Race "created" event.
     */
    public function created(Race $race): void
    {
        $this->clearDashboardCache($race->user_id);
    }

    /**
     * Handle the Race "updated" event.
     */
    public function updated(Race $race): void
    {
        $this->clearDashboardCache($race->user_id);
    }

    /**
     * Handle the Race "deleted" event.
     */
    public function deleted(Race $race): void
    {
        $this->clearDashboardCache($race->user_id);
    }

    /**
     * Handle the Race "restored" event.
     */
    public function restored(Race $race): void
    {
        $this->clearDashboardCache($race->user_id);
    }

    /**
     * Handle the Race "force deleted" event.
     */
    public function forceDeleted(Race $race): void
    {
        $this->clearDashboardCache($race->user_id);
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
