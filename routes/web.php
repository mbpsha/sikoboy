<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DataKerjasamaController;
use App\Http\Controllers\Admin\ManajemenDokumenController;
use App\Http\Controllers\Admin\ManajemenPotensiController;
use App\Http\Controllers\Admin\RiwayatKerjasamaController;
use App\Http\Controllers\Admin\StatusKontrakController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Mitra\DashboardController as MitraDashboardController;
use App\Http\Controllers\Mitra\ProfileController as MitraProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home / Welcome
Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

// Public template dokumen routes (website)
Route::get('/template-dokumen', [ManajemenDokumenController::class, 'listPublic'])
    ->name('template-dokumen.index');
Route::get('/template-dokumen/{id}/download', [ManajemenDokumenController::class, 'download'])
    ->name('template-dokumen.download');
Route::get('/template-dokumen/{id}/preview', [ManajemenDokumenController::class, 'preview'])
    ->name('template-dokumen.preview');

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

    // Dashboard
    Route::get('/beranda', [AdminDashboardController::class, 'index'])
        ->name('beranda.index');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

    // Users
    Route::get('/pengguna', [AdminUserController::class, 'index'])
        ->name('pengguna.index');
    Route::post('/pengguna', [AdminUserController::class, 'store'])
        ->name('pengguna.store');

    Route::get('/users', [AdminUserController::class, 'index'])
        ->name('users.index');
    Route::post('/users', [AdminUserController::class, 'store'])
        ->name('users.store');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])
        ->name('users.show');

    // Status Kontrak (mitra kerjasama in negotiation)
    Route::get('/status-kontrak', [StatusKontrakController::class, 'index'])
        ->name('status-kontrak.index');
    Route::put('/status-kontrak/{id}', [StatusKontrakController::class, 'update'])
        ->name('status-kontrak.update');
    Route::put('/status-kontrak/{id}/persetujuan', [StatusKontrakController::class, 'updatePersetujuan'])
        ->name('status-kontrak.persetujuan');
    Route::put('/status-kontrak/{id}/finalize', [StatusKontrakController::class, 'finalize'])
        ->name('status-kontrak.finalize');

    // Riwayat Kerjasama — Mitra (finalised)
    Route::get('/riwayat-kerjasama', [RiwayatKerjasamaController::class, 'mitra'])
        ->name('riwayat-kerjasama.index');
    Route::get('/riwayat-kerjasama/mitra', [RiwayatKerjasamaController::class, 'mitra'])
        ->name('riwayat-kerjasama.mitra');

    // Riwayat Kerjasama — Pemerintah Boyolali
    Route::get('/riwayat-kerjasama/pemerintah', [RiwayatKerjasamaController::class, 'pemerintah'])
        ->name('riwayat-kerjasama.pemerintah');
    Route::post('/riwayat-kerjasama/pemerintah', [RiwayatKerjasamaController::class, 'storePemerintah'])
        ->name('riwayat-kerjasama.pemerintah.store');
    Route::put('/riwayat-kerjasama/pemerintah/{id}', [RiwayatKerjasamaController::class, 'updatePemerintah'])
        ->name('riwayat-kerjasama.pemerintah.update');

    // Data Kerjasama (combined view)
    Route::get('/data-kerjasama/pemda', [DataKerjasamaController::class, 'index'])
        ->defaults('pemrakarsa', 'P')
        ->name('data-kerjasama.pemda');
    Route::get('/data-kerjasama/mitra', [DataKerjasamaController::class, 'index'])
        ->defaults('pemrakarsa', 'M')
        ->name('data-kerjasama.mitra');
    Route::get('/data-kerjasama', [DataKerjasamaController::class, 'index'])
        ->name('data-kerjasama.index');
    Route::post('/data-kerjasama', [DataKerjasamaController::class, 'store'])
        ->name('data-kerjasama.store');

    // Manajemen Potensi
    Route::get('/manajemen-potensi', [ManajemenPotensiController::class, 'index'])
        ->name('manajemen-potensi.index');
    Route::post('/manajemen-potensi', [ManajemenPotensiController::class, 'store'])
        ->name('manajemen-potensi.store');
    Route::put('/manajemen-potensi/{id}', [ManajemenPotensiController::class, 'update'])
        ->name('manajemen-potensi.update');
    Route::delete('/manajemen-potensi/{id}', [ManajemenPotensiController::class, 'destroy'])
        ->name('manajemen-potensi.destroy');

    // Manajemen Dokumen
    Route::get('/manajemen-dokumen', [ManajemenDokumenController::class, 'index'])
        ->name('manajemen-dokumen.index');
    Route::post('/manajemen-dokumen', [ManajemenDokumenController::class, 'store'])
        ->name('manajemen-dokumen.store');
    Route::get('/manajemen-dokumen/{id}/download', [ManajemenDokumenController::class, 'download'])
        ->name('manajemen-dokumen.download');
    Route::get('/manajemen-dokumen/{id}/preview', [ManajemenDokumenController::class, 'preview'])
        ->name('manajemen-dokumen.preview');
    Route::delete('/manajemen-dokumen/{id}', [ManajemenDokumenController::class, 'destroy'])
        ->name('manajemen-dokumen.destroy');

    // Legacy partner routes (kept for backward compatibility)
    Route::get('/partners', [AdminDashboardController::class, 'partners'])
        ->name('partners.index');
    Route::get('/partners/{id}', [AdminDashboardController::class, 'showPartner'])
        ->name('partners.show');
});
