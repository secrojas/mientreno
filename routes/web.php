<?php

use App\Http\Controllers\Auth\v1\LoginController;
use App\Http\Controllers\Auth\v1\RegisterController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

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
});

require __DIR__.'/auth.php';
