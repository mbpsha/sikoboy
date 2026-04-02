<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Mitra\DashboardController as MitraDashboardController;
use App\Http\Controllers\Mitra\ProfileController as MitraProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

// Home / Welcome
Route::get('/', fn() => Inertia::render('Welcome'))->name('home');

// Role Selection & Authentication
Route::get('/role-selection', [LoginController::class, 'showLoginForm'])->name('login.select');
Route::get('/login/{role?}', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration (Mitra only)
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.attempt');

// Password Reset Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.update');

Route::get('/dev/verify-email', function () {
    return Inertia::render('Auth/VerifyEmail');
});
    
// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'notice'])
        ->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');
});

// Partner (Mitra) Routes
Route::middleware(['auth', 'role:mitra'])->prefix('mitra')->name('mitra.')->group(function () {
    Route::get('/dashboard', [MitraDashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile/complete', [MitraDashboardController::class, 'completeProfile'])
        ->name('profile.complete');
    Route::post('/profile/complete', [MitraDashboardController::class, 'storeProfile'])
        ->name('profile.store');

    Route::get('/profile', [MitraProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::put('/profile', [MitraProfileController::class, 'update'])
        ->name('profile.update');
    Route::put('/profile/password', [MitraProfileController::class, 'updatePassword'])
        ->name('profile.password');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/partners', [AdminDashboardController::class, 'partners'])
        ->name('partners.index');
    Route::get('/partners/{id}', [AdminDashboardController::class, 'showPartner'])
        ->name('partners.show');
});