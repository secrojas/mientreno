<?php

namespace App\Observers;

use App\Models\Workout;
use Illuminate\Support\Facades\Cache;

class WorkoutObserver
{
    /**
     * Handle the Workout "created" event.
     */
    public function created(Workout $workout): void
    {
        $this->clearDashboardCache($workout->user_id);
    }

    /**
     * Handle the Workout "updated" event.
     */
    public function updated(Workout $workout): void
    {
        $this->clearDashboardCache($workout->user_id);
    }

    /**
     * Handle the Workout "deleted" event.
     */
    public function deleted(Workout $workout): void
    {
        $this->clearDashboardCache($workout->user_id);
    }

    /**
     * Handle the Workout "restored" event.
     */
    public function restored(Workout $workout): void
    {
        $this->clearDashboardCache($workout->user_id);
    }

    /**
     * Handle the Workout "force deleted" event.
     */
    public function forceDeleted(Workout $workout): void
    {
        $this->clearDashboardCache($workout->user_id);
    }

    /**
     * Clear dashboard cache for the user
     */
    protected function clearDashboardCache(int $userId): void
    {
        $currentWeek = now()->weekOfYear();
        $currentYear = now()->year;
        $currentMonth = now()->month;

        // Clear dashboard cache
        Cache::forget("dashboard_data_user_{$userId}_week_{$currentWeek}");

        // Clear current week report cache
        Cache::forget("report_weekly_user_{$userId}_year_{$currentYear}_week_{$currentWeek}");

        // Clear current month report cache
        Cache::forget("report_monthly_user_{$userId}_year_{$currentYear}_month_{$currentMonth}");

        // Clear previous week (in case workout was from last week)
        $prevWeek = $currentWeek - 1;
        $prevYear = $currentYear;
        if ($prevWeek < 1) {
            $prevWeek = 52;
            $prevYear = $currentYear - 1;
        }
        Cache::forget("dashboard_data_user_{$userId}_week_{$prevWeek}");
        Cache::forget("report_weekly_user_{$userId}_year_{$prevYear}_week_{$prevWeek}");

        // Clear previous month (in case workout was from last month)
        $prevMonth = $currentMonth - 1;
        $prevMonthYear = $currentYear;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevMonthYear = $currentYear - 1;
        }
        Cache::forget("report_monthly_user_{$userId}_year_{$prevMonthYear}_month_{$prevMonth}");
    }
}
