<?php

// routes/admin.php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

// Guest routes for admins (registration, login, password reset, etc.)
Route::middleware('guest:admin')->prefix('admin')->name('admin.')->group(function () {
    // Registration
    Route::get('register', [AdminController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AdminController::class, 'store']);

    // Login
    Route::get('login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminController::class, 'login']);

    // Password reset
    Route::get('forgot-password', [AdminController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('forgot-password', [AdminController::class, 'forgotPassword'])->name('password.email');
    Route::get('reset-password/{token}', [AdminController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('reset-password', [AdminController::class, 'resetPassword'])->name('password.store');
});

// Authenticated routes for admins (dashboard, profile management, etc.)
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Email verification
    Route::get('verify-email', [AdminController::class, 'verificationNotice'])->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', [AdminController::class, 'verifyEmail'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [AdminController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');

    // Password confirmation
    Route::get('confirm-password', [AdminController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [AdminController::class, 'store']);

    // Password update
    Route::put('password', [AdminController::class, 'update'])->name('password.update');

    // Logout
    Route::post('logout', [AdminController::class, 'destroy'])->name('logout');
});
