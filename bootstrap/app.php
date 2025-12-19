<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'set.business' => \App\Http\Middleware\SetCurrentBusiness::class,
            'business.context' => \App\Http\Middleware\SetBusinessContext::class,
            'individual' => \App\Http\Middleware\IndividualUser::class,
            'business.user' => \App\Http\Middleware\BusinessUser::class,
            'coach' => \App\Http\Middleware\CoachMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        
    })
    ->withProviders([
        App\Providers\BusinessServiceProvider::class,
        // App\Providers\RepositoryServiceProvider::class,
    ])
    ->create();
