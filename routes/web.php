<?php

use App\Http\Controllers\Auth\v1\LoginController;
use App\Http\Controllers\Auth\v1\RegisterController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Landing page v2 (improved design)
Route::get('/v2', function () {
    return view('welcomev2');
})->name('welcome.v2');

// Authentication routes (no business required)
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ============================================================================
// RUTAS PARA USUARIOS INDIVIDUALES (sin business)
// ============================================================================
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Workouts resource
    Route::resource('workouts', \App\Http\Controllers\WorkoutController::class);

    // Workout status actions
    Route::get('/workouts/{workout}/mark-completed', [\App\Http\Controllers\WorkoutController::class, 'showMarkCompleted'])->name('workouts.mark-completed');
    Route::post('/workouts/{workout}/mark-completed', [\App\Http\Controllers\WorkoutController::class, 'markCompleted']);
    Route::post('/workouts/{workout}/mark-skipped', [\App\Http\Controllers\WorkoutController::class, 'markSkipped'])->name('workouts.mark-skipped');

    // Other resources
    Route::resource('races', \App\Http\Controllers\RaceController::class);
    Route::resource('goals', \App\Http\Controllers\GoalController::class);

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::get('/weekly', [\App\Http\Controllers\ReportController::class, 'weekly'])->name('weekly');
        Route::get('/weekly/{year}/{week}', [\App\Http\Controllers\ReportController::class, 'weekly'])->name('weekly.period');
        Route::get('/weekly/{year}/{week}/pdf', [\App\Http\Controllers\ReportController::class, 'exportWeeklyPDF'])->name('weekly.pdf');
        Route::post('/weekly/{year}/{week}/share', [\App\Http\Controllers\ReportController::class, 'shareWeekly'])->name('weekly.share');
        Route::get('/monthly', [\App\Http\Controllers\ReportController::class, 'monthly'])->name('monthly');
        Route::get('/monthly/{year}/{month}', [\App\Http\Controllers\ReportController::class, 'monthly'])->name('monthly.period');
        Route::get('/monthly/{year}/{month}/pdf', [\App\Http\Controllers\ReportController::class, 'exportMonthlyPDF'])->name('monthly.pdf');
        Route::post('/monthly/{year}/{month}/share', [\App\Http\Controllers\ReportController::class, 'shareMonthly'])->name('monthly.share');
    });

    // Coach routes (sin prefijo business, para creación de business)
    Route::prefix('coach')->name('coach.')->middleware('coach')->group(function () {
        Route::get('/business/create', [\App\Http\Controllers\Coach\BusinessController::class, 'create'])->name('business.create');
        Route::post('/business', [\App\Http\Controllers\Coach\BusinessController::class, 'store'])->name('business.store');
    });
});

// ============================================================================
// RUTAS MULTI-TENANT (con prefijo /{business})
// ============================================================================
Route::prefix('{business}')->name('business.')->middleware(['auth', 'business.context'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Workouts
    Route::resource('workouts', \App\Http\Controllers\WorkoutController::class);
    Route::get('/workouts/{workout}/mark-completed', [\App\Http\Controllers\WorkoutController::class, 'showMarkCompleted'])->name('workouts.mark-completed');
    Route::post('/workouts/{workout}/mark-completed', [\App\Http\Controllers\WorkoutController::class, 'markCompleted']);
    Route::post('/workouts/{workout}/mark-skipped', [\App\Http\Controllers\WorkoutController::class, 'markSkipped'])->name('workouts.mark-skipped');

    // Races and Goals
    Route::resource('races', \App\Http\Controllers\RaceController::class);
    Route::resource('goals', \App\Http\Controllers\GoalController::class);

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::get('/weekly', [\App\Http\Controllers\ReportController::class, 'weekly'])->name('weekly');
        Route::get('/weekly/{year}/{week}', [\App\Http\Controllers\ReportController::class, 'weekly'])->name('weekly.period');
        Route::get('/weekly/{year}/{week}/pdf', [\App\Http\Controllers\ReportController::class, 'exportWeeklyPDF'])->name('weekly.pdf');
        Route::post('/weekly/{year}/{week}/share', [\App\Http\Controllers\ReportController::class, 'shareWeekly'])->name('weekly.share');
        Route::get('/monthly', [\App\Http\Controllers\ReportController::class, 'monthly'])->name('monthly');
        Route::get('/monthly/{year}/{month}', [\App\Http\Controllers\ReportController::class, 'monthly'])->name('monthly.period');
        Route::get('/monthly/{year}/{month}/pdf', [\App\Http\Controllers\ReportController::class, 'exportMonthlyPDF'])->name('monthly.pdf');
        Route::post('/monthly/{year}/{month}/share', [\App\Http\Controllers\ReportController::class, 'shareMonthly'])->name('monthly.share');
    });

    // Coach routes (con prefijo business)
    Route::prefix('coach')->name('coach.')->middleware('coach')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Coach\DashboardController::class, 'index'])->name('dashboard');

        // Business management (el business ya viene del prefijo del grupo principal)
        Route::get('/business', [\App\Http\Controllers\Coach\BusinessController::class, 'show'])->name('business.show');
        Route::get('/business/edit', [\App\Http\Controllers\Coach\BusinessController::class, 'edit'])->name('business.edit');
        Route::put('/business', [\App\Http\Controllers\Coach\BusinessController::class, 'update'])->name('business.update');
        Route::delete('/business', [\App\Http\Controllers\Coach\BusinessController::class, 'destroy'])->name('business.destroy');

        // Training Groups
        Route::resource('groups', \App\Http\Controllers\Coach\TrainingGroupController::class);
        Route::post('/groups/{group}/members', [\App\Http\Controllers\Coach\TrainingGroupController::class, 'addMember'])->name('groups.addMember');
        Route::delete('/groups/{group}/members/{user}', [\App\Http\Controllers\Coach\TrainingGroupController::class, 'removeMember'])->name('groups.removeMember');

        // Subscriptions
        Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Coach\SubscriptionController::class, 'index'])->name('index');
            Route::get('/plans', [\App\Http\Controllers\Coach\SubscriptionController::class, 'plans'])->name('plans');
            Route::post('/subscribe', [\App\Http\Controllers\Coach\SubscriptionController::class, 'subscribe'])->name('subscribe');
            Route::post('/cancel', [\App\Http\Controllers\Coach\SubscriptionController::class, 'cancel'])->name('cancel');
        });
    });
});

// Public shared report (no auth required)
Route::get('/share/{token}', [\App\Http\Controllers\ReportController::class, 'showShared'])->name('reports.shared');

// Deploy webhook (no auth required, uses token)
Route::post('/deploy/webhook', [\App\Http\Controllers\DeployController::class, 'deploy'])->name('deploy.webhook');
Route::get('/deploy/ping', [\App\Http\Controllers\DeployController::class, 'ping'])->name('deploy.ping');

require __DIR__.'/auth.php';
