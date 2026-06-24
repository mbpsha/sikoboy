<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DataKerjasamaController;
use App\Http\Controllers\Admin\ManajemenDokumenController;
use App\Http\Controllers\Admin\ManajemenPotensiController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\PeraturanController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\RiwayatKerjasamaController;
use App\Http\Controllers\Admin\StatusKontrakController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Mitra\DashboardController as MitraDashboardController;
use App\Http\Controllers\Mitra\KerjasamaController as MitraKerjasamaController;
use App\Http\Controllers\Mitra\ProfileController as MitraProfileController;
use App\Http\Controllers\TemplateDokumenController;
use App\Models\Peraturan;
use App\Models\Potensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

$loginThrottleAttempts = 6;

// ========================================
// PUBLIC ROUTES
// ========================================

// Home / Welcome
Route::get('/', function () {
    $potensi = Potensi::query()
        ->with('poin')
        ->where('status_tampil', true)
        ->orderBy('kategori')
        ->orderBy('id_potensi')
        ->get()
        ->groupBy('kategori')
        ->map(function ($items) {
            return $items->map(function (Potensi $p) {
                return [
                    'id_potensi' => $p->id_potensi,
                    'kategori' => $p->kategori,
                    'judul' => $p->judul,
                    'deskripsi' => $p->deskripsi,
                    'gambar_url' => $p->gambar_path ? asset('storage/' . $p->gambar_path) : null,
                    'poin' => $p->poin->map(fn($pt) => [
                        'id' => $pt->id_potensi_poin,
                        'isi' => $pt->isi,
                    ])->values(),
                ];
            })->values();
        });

    // STATISTIK DINAMIS
    $today = now();
    $sixMonthsLater = now()->addMonths(6);
    $threeMonthsLater = now()->addMonths(3);

    $totalKerjasama = DB::table('kerjasama')->count();

    $lessThanSixMonths = DB::table('kerjasama')
        ->join('periode_kerjasama', 'kerjasama.id_kerjasama', '=', 'periode_kerjasama.id_kerjasama')
        ->whereBetween('periode_kerjasama.tanggal_berakhir', [$today, $sixMonthsLater])
        ->where('kerjasama.status_aktif', true)
        ->distinct('kerjasama.id_kerjasama')
        ->count('kerjasama.id_kerjasama');

    $lessThanThreeMonths = DB::table('kerjasama')
        ->join('periode_kerjasama', 'kerjasama.id_kerjasama', '=', 'periode_kerjasama.id_kerjasama')
        ->whereBetween('periode_kerjasama.tanggal_berakhir', [$today, $threeMonthsLater])
        ->where('kerjasama.status_aktif', true)
        ->distinct('kerjasama.id_kerjasama')
        ->count('kerjasama.id_kerjasama');

    $expired = DB::table('kerjasama')
        ->join('periode_kerjasama', 'kerjasama.id_kerjasama', '=', 'periode_kerjasama.id_kerjasama')
        ->where('periode_kerjasama.tanggal_berakhir', '<', $today)
        ->where('kerjasama.status_aktif', true)
        ->distinct('kerjasama.id_kerjasama')
        ->count('kerjasama.id_kerjasama');

    $stats = [
        ['label' => 'Jumlah Kerja Sama', 'value' => $totalKerjasama],
        ['label' => 'Masa Berlaku <6 Bulan', 'value' => $lessThanSixMonths],
        ['label' => 'Masa Berlaku <3 Bulan', 'value' => $lessThanThreeMonths],
        ['label' => 'Masa Berlaku Habis', 'value' => $expired],
    ];

    return Inertia::render('Welcome', [
        'potensiData' => $potensi,
        'stats' => $stats,
    ]);
})->name('home');

// About
Route::get('/about', fn() => Inertia::render('About'))->name('about');

// Kontak
Route::get('/kontak', fn() => Inertia::render('Kontak'))->name('kontak');

// Peraturan
Route::get('/peraturan', function () {
    return Inertia::render('Peraturan', [
        'peraturans' => Peraturan::latest()->get(),
    ]);
})->name('peraturan');

// Public template dokumen routes (website)
Route::get('/dokumen', fn() => redirect()->route('template-dokumen.index'))
    ->name('dokumen.index');
Route::get('/template-dokumen', [ManajemenDokumenController::class, 'listPublic'])
    ->name('template-dokumen.index');
Route::get('/template-dokumen/{id}/download', [TemplateDokumenController::class, 'download'])
    ->name('template-dokumen.download');
Route::get('/template-dokumen/{id}/preview', [TemplateDokumenController::class, 'preview'])
    ->name('template-dokumen.preview');

Route::middleware('auth')->get('/portal-mitra', function (\Illuminate\Http\Request $request) {
    return match ($request->user()?->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'mitra' => redirect()->route('mitra.profile.index'),
        default => redirect()->route('home'),
    };
})->name('portal-mitra');

// Role Selection & Authentication
Route::get('/role-selection', [LoginController::class, 'showLoginForm'])->name('login.select');
Route::get('/login/{role?}', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration (Mitra only)
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])
    ->middleware('throttle:' . $loginThrottleAttempts . ',1')
    ->name('register.attempt');

// Password Reset
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->middleware('throttle:' . $loginThrottleAttempts . ',1')
    ->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->middleware('throttle:' . $loginThrottleAttempts . ',1')
    ->name('password.update');

// Email Verification
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

// Dev: Verify Email Page
if (app()->environment(['local', 'testing'])) {
    Route::get('/dev/verify-email', function () {
        return Inertia::render('Auth/VerifyEmail');
    });
}

// ========================================
// AUTHENTICATED USER PROFILE
// ========================================

Route::middleware('auth')->get('/profile', function (Request $request) {
    $user = $request->user();

    return Inertia::render('Profile/UserProfil', [
        'user' => $user,
        'mitra' => $user?->mitra,
    ]);
})->name('profile.show');

// ========================================
// MITRA ROUTES
// ========================================

Route::middleware(['auth', 'role:mitra', 'throttle:240,1'])->prefix('mitra')->name('mitra.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [MitraDashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile/complete', [MitraProfileController::class, 'completeProfile'])
        ->name('profile.complete');
    Route::post('/profile/complete', [MitraProfileController::class, 'storeProfile'])
        ->name('profile.store');
    Route::get('/profile', [MitraProfileController::class, 'index'])
        ->name('profile.index');
    Route::get('/profile/edit', [MitraProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::put('/profile', [MitraProfileController::class, 'update'])
        ->name('profile.update');
    Route::put('/profile/password', [MitraProfileController::class, 'updatePassword'])
        ->name('profile.password');

    // 🔔 Notifikasi
    Route::get('/notifications', [MitraProfileController::class, 'notifications'])
        ->name('notifications');
    Route::post('/notifications/mark-read/{id}', [MitraProfileController::class, 'markNotificationAsRead'])
        ->name('notifications.mark-read');

    // Pengajuan Kerjasama
    Route::get('/pengajuan/step1', [MitraKerjasamaController::class, 'createStep1'])
        ->name('pengajuan.step1');
    Route::post('/pengajuan/step1', [MitraKerjasamaController::class, 'storeStep1'])
        ->name('pengajuan.step1.store');
    Route::get('/pengajuan/step2', [MitraKerjasamaController::class, 'createStep2'])
        ->name('pengajuan.step2');
    Route::post('/pengajuan', [MitraKerjasamaController::class, 'store'])
        ->name('pengajuan.store');

    // Upload revisi dokumen untuk kerjasama (Mitra)
    Route::post('/kerjasama/{id}/revisi', [MitraKerjasamaController::class, 'uploadRevision'])
        ->name('kerjasama.revisi.upload');
});

// ========================================
// ADMIN ROUTES
// ========================================

Route::middleware(['auth', 'role:admin', 'throttle:240,1'])->prefix('admin')->name('admin.')->group(function () {

    // 🔹 Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

    // 🔹 Notifikasi Admin
    Route::get('/notifikasi', [AdminNotificationController::class, 'index'])
        ->name('notifications.index');

    // 🔹 Pengguna (Users)
    Route::get('/pengguna', [AdminUserController::class, 'index'])
        ->middleware('throttle:120,1')
        ->name('pengguna.index');
    Route::post('/pengguna', [AdminUserController::class, 'store'])
        ->name('pengguna.store');
    Route::get('/pengguna/{id}', [AdminUserController::class, 'show'])
        ->name('pengguna.show');
    Route::put('/pengguna/{id}', [AdminUserController::class, 'update'])
        ->name('pengguna.update');
    Route::put('/pengguna/{id}/status', [AdminUserController::class, 'updateStatus'])
        ->name('pengguna.update-status');
    Route::put('/pengguna/{id}/verify', [AdminUserController::class, 'verifyMitra'])
        ->name('pengguna.verify');
    Route::delete('/pengguna/{id}/terminate', [AdminUserController::class, 'terminate'])
        ->name('pengguna.terminate');
    Route::delete('/pengguna/{id}', [AdminUserController::class, 'destroy'])
        ->name('pengguna.destroy');

    // Legacy route names for backward compatibility
    Route::get('/users', [AdminUserController::class, 'index'])
        ->middleware('throttle:120,1')
        ->name('users.index');
    Route::post('/users', [AdminUserController::class, 'store'])
        ->name('users.store');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])
        ->name('users.show');
    Route::put('/users/{id}', [AdminUserController::class, 'update'])
        ->name('users.update');
    Route::put('/users/{id}/status', [AdminUserController::class, 'updateStatus'])
        ->name('users.update-status');
    Route::put('/users/{id}/verify', [AdminUserController::class, 'verifyMitra'])
        ->name('users.verify');
    Route::delete('/users/{id}/terminate', [AdminUserController::class, 'terminate'])
        ->name('users.terminate');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])
        ->name('users.destroy');

    // Profil Admin
    Route::get('/profile', [AdminProfileController::class, 'show'])
        ->name('profile.show');
    Route::put('/profile', [AdminProfileController::class, 'update'])
        ->name('profile.update');
    Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])
        ->name('profile.password');

    // Status Kontrak
    Route::get('/status-kontrak', [StatusKontrakController::class, 'index'])
        ->middleware('throttle:120,1')
        ->name('status-kontrak.index');
    Route::put('/status-kontrak/{id}', [StatusKontrakController::class, 'update'])
        ->name('status-kontrak.update');
    Route::put('/status-kontrak/{id}/persetujuan', [StatusKontrakController::class, 'updatePersetujuan'])
        ->name('status-kontrak.persetujuan');
    Route::put('/status-kontrak/{id}/finalize', [StatusKontrakController::class, 'finalize'])
        ->name('status-kontrak.finalize');

    // 🔹 Riwayat Kerjasama
    Route::get('/riwayat-kerjasama/gabungan', [RiwayatKerjasamaController::class, 'index'])
        ->middleware('throttle:120,1')
        ->name('riwayat-kerjasama.gabungan');
    Route::get('/riwayat-kerjasama/gabungan/export', [RiwayatKerjasamaController::class, 'exportGabungan'])
        ->middleware('throttle:30,1')
        ->name('riwayat-kerjasama.gabungan.export');
    Route::post('/riwayat-kerjasama/gabungan', [RiwayatKerjasamaController::class, 'storeGabungan'])
        ->name('riwayat-kerjasama.gabungan.store');
    Route::put('/riwayat-kerjasama/gabungan/{id}', [RiwayatKerjasamaController::class, 'updateGabungan'])
        ->name('riwayat-kerjasama.gabungan.update');
    Route::get('/riwayat-kerjasama/mitra', [RiwayatKerjasamaController::class, 'mitra'])
        ->middleware('throttle:120,1')
        ->name('riwayat-kerjasama.mitra');
    Route::get('/riwayat-kerjasama/mitra/export', [RiwayatKerjasamaController::class, 'exportMitra'])
        ->middleware('throttle:30,1')
        ->name('riwayat-kerjasama.mitra.export');
    Route::post('/riwayat-kerjasama/mitra', [RiwayatKerjasamaController::class, 'storeMitra'])
        ->name('riwayat-kerjasama.mitra.store');
    Route::get('/riwayat-kerjasama/pemerintah', [RiwayatKerjasamaController::class, 'pemerintah'])
        ->middleware('throttle:120,1')
        ->name('riwayat-kerjasama.pemerintah');
    Route::get('/riwayat-kerjasama/pemerintah/export', [RiwayatKerjasamaController::class, 'exportPemerintah'])
        ->middleware('throttle:30,1')
        ->name('riwayat-kerjasama.pemerintah.export');
    Route::post('/riwayat-kerjasama/pemerintah', [RiwayatKerjasamaController::class, 'storePemerintah'])
        ->name('riwayat-kerjasama.pemerintah.store');
    Route::put('/riwayat-kerjasama/pemerintah/{id}', [RiwayatKerjasamaController::class, 'updatePemerintah'])
        ->name('riwayat-kerjasama.pemerintah.update');
    Route::put('/riwayat-kerjasama/{id}/status', [RiwayatKerjasamaController::class, 'updateStatus'])
        ->name('riwayat-kerjasama.update-status');
    Route::post('/riwayat-kerjasama/adendum', [RiwayatKerjasamaController::class, 'storeAdendum'])
        ->name('riwayat-kerjasama.adendum.store');

    // 🔹 Data Kerjasama
    Route::get('/data-kerjasama', [DataKerjasamaController::class, 'index'])
        ->middleware('throttle:120,1')
        ->name('data-kerjasama.index');
    Route::get('/data-kerjasama/pemda', [DataKerjasamaController::class, 'index'])
        ->middleware('throttle:120,1')
        ->defaults('pemrakarsa', 'P')
        ->name('data-kerjasama.pemda');
    Route::get('/data-kerjasama/mitra', [DataKerjasamaController::class, 'index'])
        ->middleware('throttle:120,1')
        ->defaults('pemrakarsa', 'M')
        ->name('data-kerjasama.mitra');
    Route::post('/data-kerjasama', [DataKerjasamaController::class, 'store'])
        ->name('data-kerjasama.store');
    Route::put('/data-kerjasama/{id}/nomor-surat', [DataKerjasamaController::class, 'updateNomorSurat'])
        ->name('data-kerjasama.nomor-surat');
    // Proses Kerjasama — gunakan POST untuk semua karena ada file upload
    Route::post(
        '/data-kerjasama/{id}/proses',
        [DataKerjasamaController::class, 'storeProcess']
    )
        ->name('data-kerjasama.proses.store');
    Route::put(
        '/data-kerjasama/{id}/proses/{prosesId}',
        [DataKerjasamaController::class, 'updateProcess']
    )
        ->name('data-kerjasama.proses.update');

    // 🔹 Manajemen Potensi
    Route::get('/manajemen-potensi', [ManajemenPotensiController::class, 'index'])
        ->middleware('throttle:120,1')
        ->name('manajemen-potensi.index');
    Route::get('/manajemen-potensi/list', [ManajemenPotensiController::class, 'list'])
        ->name('manajemen-potensi.list');
    Route::post('/manajemen-potensi', [ManajemenPotensiController::class, 'store'])
        ->name('manajemen-potensi.store');
    Route::put('/manajemen-potensi/{id}', [ManajemenPotensiController::class, 'update'])
        ->name('manajemen-potensi.update');
    Route::delete('/manajemen-potensi/{id}', [ManajemenPotensiController::class, 'destroy'])
        ->name('manajemen-potensi.destroy');

    // 🔹 Manajemen Dokumen
    Route::get('/manajemen-dokumen', [ManajemenDokumenController::class, 'index'])
        ->middleware('throttle:120,1')
        ->name('manajemen-dokumen.index');
    Route::get('/manajemen-dokumen/list', [ManajemenDokumenController::class, 'list'])
        ->name('manajemen-dokumen.list');
    Route::post('/manajemen-dokumen', [ManajemenDokumenController::class, 'store'])
        ->name('manajemen-dokumen.store');
    Route::put('/manajemen-dokumen/{id}', [ManajemenDokumenController::class, 'update'])
        ->name('manajemen-dokumen.update');
    Route::get('/manajemen-dokumen/{id}/download', [ManajemenDokumenController::class, 'download'])
        ->name('manajemen-dokumen.download');
    Route::get('/manajemen-dokumen/{id}/preview', [ManajemenDokumenController::class, 'preview'])
        ->name('manajemen-dokumen.preview');
    Route::delete('/manajemen-dokumen/{id}', [ManajemenDokumenController::class, 'destroy'])
        ->name('manajemen-dokumen.destroy');

    // Manajemen Peraturan
    Route::get('/manajemen-peraturan', [PeraturanController::class, 'index'])
        ->name('manajemen-peraturan.index');
    Route::get('/manajemen-peraturan/list', [PeraturanController::class, 'list'])
        ->name('manajemen-peraturan.list');
    Route::post('/manajemen-peraturan', [PeraturanController::class, 'store'])
        ->name('manajemen-peraturan.store');
    Route::post('/manajemen-peraturan/{peraturan}', [PeraturanController::class, 'update'])
        ->name('manajemen-peraturan.update');
    Route::delete('/manajemen-peraturan/{peraturan}', [PeraturanController::class, 'destroy'])
        ->name('manajemen-peraturan.destroy');

    // Legacy routes (backward Compability)
    Route::get('/partners', [AdminDashboardController::class, 'partners'])
        ->name('partners.index');
    Route::get('/partners/{id}', [AdminDashboardController::class, 'showPartner'])
        ->name('partners.show');

    // Detail Notifikasi
    Route::get('/notifikasi/{id}',[AdminNotificationController::class, 'show']
        )->name('notifications.show');
});