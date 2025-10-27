<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\DesignSpecController; // ← tambahan

Route::get('/', fn () => redirect()->route('login'))->name('root');

/*
|--------------------------------------------------------------------------
| Guest routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'loginPage'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register',  [AuthController::class, 'registerPage'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Google OAuth
    Route::get('/login/google',          [AuthController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/login/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('login.google.callback');
});

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Dashboard & Profile
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Notifications (dummy page)
    Route::view('/notifications', 'notifications.index')->name('notifications.index');

    // Projects
    Route::resource('projects', ProjectController::class)->only(['index','store','update','destroy']);
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

    // SDLC tab routes
    Route::get('/projects/{project}/sdlc/{phase}', [ProjectController::class, 'showPhase'])
        ->where('phase', 'planning|requirement|design|development|testing|deployment|maintenance')
        ->name('projects.sdlc');

    // Requirements CRUD (nested)
    Route::post('/projects/{project}/requirements', [RequirementController::class, 'store'])
        ->name('projects.requirements.store');
    Route::put('/projects/{project}/requirements/{requirement}', [RequirementController::class, 'update'])
        ->name('projects.requirements.update');
    Route::delete('/projects/{project}/requirements/{requirement}', [RequirementController::class, 'destroy'])
        ->name('projects.requirements.destroy');

    // Design Specs CRUD (nested & shallow)
    Route::resource('projects.design-specs', DesignSpecController::class)
        ->only(['store','update','destroy'])
        ->shallow();

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
