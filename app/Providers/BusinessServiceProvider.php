<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\BusinessContext;

class BusinessServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(BusinessContext::class, fn () => new BusinessContext());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
