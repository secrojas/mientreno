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

// Protected routes
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
});

// Public shared report (no auth required)
Route::get('/share/{token}', [\App\Http\Controllers\ReportController::class, 'showShared'])->name('reports.shared');

require __DIR__.'/auth.php';
