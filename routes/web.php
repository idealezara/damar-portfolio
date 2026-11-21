<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

// ---- Public site ----
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/projects/{project:slug}', [ProjectController::class, 'show'])->name('projects.show');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

// ---- Admin ----
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'create'])->name('login');
        Route::post('login', [AuthController::class, 'store'])->name('login.store');
    });

    Route::middleware('admin')->group(function () {
        Route::redirect('/', '/admin/dashboard');
        Route::post('logout', [AuthController::class, 'destroy'])->name('logout');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::patch('projects/{project}/status', [AdminProjectController::class, 'toggleStatus'])->name('projects.status');
        Route::resource('projects', AdminProjectController::class);
        Route::resource('skills', SkillController::class)->except('show');

        Route::resource('messages', MessageController::class)->only(['index', 'show', 'destroy']);
        Route::patch('messages/{message}/read', [MessageController::class, 'toggleRead'])->name('messages.read');

        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    });
});
