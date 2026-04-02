<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OpdController;
use App\Http\Controllers\Admin\PerijinanController;
use App\Http\Controllers\Admin\DataPerijinanController;
use App\Http\Controllers\Admin\BeritaController;
use App\Http\Controllers\Admin\RegulasiController;
use App\Http\Controllers\Admin\PengaduanController as AdminPengaduanController;
use App\Http\Controllers\Admin\DataSkmController;
use App\Http\Controllers\Admin\HasilSkmController;
use App\Http\Controllers\Pemohon\DashboardController as PemohonDashboardController;
use App\Http\Controllers\Pemohon\ProfileController as PemohonProfileController;
use App\Http\Controllers\PemohonController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ApplicationSettingsController;
use App\Http\Controllers\Admin\PengaduanHandlerController;
use App\Http\Controllers\Front\LandingPageController;
use App\Http\Controllers\Front\InformasiController;
use App\Http\Controllers\Front\LayananController;
use App\Http\Controllers\Front\SkmController;
use App\Http\Controllers\Front\AuthController;
use App\Http\Controllers\Front\PengaduanController as FrontPengaduanController;
use App\Http\Controllers\Front\RegulasiController as FrontRegulasiController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Front-end Register routes (for pemohon)
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('front.register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/api/nik/check', [AuthController::class, 'checkNik'])->name('api.nik.check');
Route::get('/api/refresh-captcha', [AuthController::class, 'refreshCaptcha'])->name('api.refresh-captcha');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard - Admin only, pemohon redirected to their own dashboard
    Route::middleware(['admin.role'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Profile Routes (Admin Only)
    Route::middleware(['admin.role'])->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::get('/change-password', [ProfileController::class, 'editPassword'])->name('change-password');
        Route::put('/update-password', [ProfileController::class, 'updatePassword'])->name('update-password');
    });

    // Settings Routes (Admin Only)
    Route::middleware(['admin.role'])->prefix('settings')->name('settings.')->group(function () {
        Route::get('/application', [ApplicationSettingsController::class, 'index'])->name('application');
        Route::put('/application', [ApplicationSettingsController::class, 'update'])->name('application.update');
        Route::get('/pengaduan-handlers', [PengaduanHandlerController::class, 'index'])->name('pengaduan-handlers');
        Route::post('/pengaduan-handlers/assign', [PengaduanHandlerController::class, 'assign'])->name('pengaduan-handlers.assign');
        Route::delete('/pengaduan-handlers/remove/{userId}', [PengaduanHandlerController::class, 'remove'])->name('pengaduan-handlers.remove');
        Route::get('/database', [SettingsController::class, 'database'])->name('database');
        Route::get('/logs', [SettingsController::class, 'logs'])->name('logs');
        Route::post('/backup/database', [SettingsController::class, 'backupDatabase'])->name('backup.database');
        Route::post('/backup/aplikasi', [SettingsController::class, 'backupAplikasi'])->name('backup.aplikasi');
        Route::post('/backup/full', [SettingsController::class, 'backupFull'])->name('backup.full');
        Route::get('/backup/{type}/{filename}/download', [SettingsController::class, 'downloadBackup'])->name('backup.download');
        Route::delete('/backup/{type}/{filename}/delete', [SettingsController::class, 'deleteBackup'])->name('backup.delete');
        Route::get('/backup/{type}/list', [SettingsController::class, 'listBackups'])->name('backup.list');
    });

    // OPD Routes (Admin Only)
    Route::middleware(['admin.role'])->group(function () {
        Route::resource('opd', OpdController::class);
    });

    // Perijinan Routes (Admin Only)
    Route::middleware(['admin.role'])->group(function () {
        Route::resource('perijinan', PerijinanController::class);
        Route::get('perijinan/{id}/form-builder', [PerijinanController::class, 'formBuilder'])->name('perijinan.form-builder');
        Route::post('perijinan/{id}/form-field', [PerijinanController::class, 'storeFormField'])->name('perijinan.form-field.store');
        Route::put('perijinan/{id}/form-field/{fieldId}', [PerijinanController::class, 'updateFormField'])->name('perijinan.form-field.update');
        Route::delete('perijinan/{id}/form-field/{fieldId}', [PerijinanController::class, 'deleteFormField'])->name('perijinan.form-field.delete');
        Route::post('perijinan/{id}/form-field/reorder', [PerijinanController::class, 'reorderFormFields'])->name('perijinan.form-field.reorder');
        Route::get('perijinan/{id}/alur-validasi', [PerijinanController::class, 'alurValidasi'])->name('perijinan.alur-validasi');
        Route::post('perijinan/{id}/validation-flow', [PerijinanController::class, 'storeValidationFlow'])->name('perijinan.validation-flow.store');
        Route::put('perijinan/{id}/validation-flow/{flowId}', [PerijinanController::class, 'updateValidationFlow'])->name('perijinan.validation-flow.update');
        Route::delete('perijinan/{id}/validation-flow/{flowId}', [PerijinanController::class, 'deleteValidationFlow'])->name('perijinan.validation-flow.delete');
        Route::post('perijinan/{id}/validation-flow/reorder', [PerijinanController::class, 'reorderValidationFlows'])->name('perijinan.validation-flow.reorder');
    });

    // Berita Routes (Admin Only)
    Route::middleware(['admin.role'])->group(function () {
        Route::resource('berita', BeritaController::class);
        Route::post('berita/{id}/toggle-slider', [BeritaController::class, 'toggleSlider'])->name('berita.toggle-slider');
    });

    // Regulasi Routes (Admin Only)
    Route::middleware(['admin.role'])->group(function () {
        Route::resource('regulasi', RegulasiController::class);
        Route::get('regulasi/{id}/download', [RegulasiController::class, 'download'])->name('regulasi.download');
    });

    // SKM Routes (Admin Only)
    Route::middleware(['admin.role'])->prefix('skm')->name('skm.')->group(function () {
        // Data SKM (Pertanyaan)
        Route::resource('data', DataSkmController::class);

        // Hasil SKM (Jawaban)
        Route::resource('hasil', HasilSkmController::class)->only(['index', 'show', 'destroy']);
        Route::get('hasil/statistics', [HasilSkmController::class, 'statistics'])->name('hasil.statistics');
        Route::get('hasil/export', [HasilSkmController::class, 'export'])->name('hasil.export');
    });

    // Pengguna Routes (Admin Only)
    Route::middleware(['admin.role'])->prefix('pengguna')->name('pengguna.')->group(function () {
        Route::resource('data', PenggunaController::class);
    });

    // Data Perijinan Routes (Admin Only)
    Route::middleware(['admin.role'])->prefix('data-perijinan')->name('data-perijinan.')->group(function () {
        Route::get('/dalam-proses', [DataPerijinanController::class, 'dalamProses'])->name('dalam-proses');
        Route::get('/perlu-perbaikan', [DataPerijinanController::class, 'perluPerbaikan'])->name('perlu-perbaikan');
        Route::get('/selesai', [DataPerijinanController::class, 'selesai'])->name('selesai');
        Route::get('/ditolak', [DataPerijinanController::class, 'ditolak'])->name('ditolak');
        Route::get('/{id}', [DataPerijinanController::class, 'show'])->name('show');
        Route::post('/{id}/validate', [DataPerijinanController::class, 'processValidation'])->name('validate');
        Route::patch('/{id}/status', [DataPerijinanController::class, 'updateStatus'])->name('update-status');
        Route::get('/download/{filepath}', [DataPerijinanController::class, 'downloadFile'])->name('download-file')->where('filepath', '.*');
    });

    // Pemohon Routes (accessible by authenticated pemohon users)
    Route::middleware(['auth'])->prefix('pemohon')->name('pemohon.')->group(function () {
        Route::get('/dashboard', [PemohonDashboardController::class, 'index'])->name('dashboard');
        // Perijinan Routes for Pemohon
        Route::get('/perijinan', [PemohonDashboardController::class, 'perijinan'])->name('perijinan');
        Route::get('/perijinan/{id}/detail', [PemohonDashboardController::class, 'perijinanDetail'])->name('perijinan.detail');
        // Pengajuan Routes for Pemohon
        Route::get('/pengajuan/create/{perijinanId}', [PemohonDashboardController::class, 'createPengajuan'])->name('pengajuan.create');
        Route::post('/pengajuan', [PemohonDashboardController::class, 'storePengajuan'])->name('pengajuan.store');
        Route::get('/pengajuan/success/{id}', [PemohonDashboardController::class, 'successPengajuan'])->name('pengajuan.success');
        // Tracking Routes for Pemohon
        Route::get('/tracking', [PemohonDashboardController::class, 'tracking'])->name('tracking');
        Route::get('/tracking/{id}', [PemohonDashboardController::class, 'trackingDetail'])->name('tracking.detail');
        // Profile Routes for Pemohon
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [PemohonProfileController::class, 'show'])->name('show');
            Route::get('/edit', [PemohonProfileController::class, 'edit'])->name('edit');
            Route::put('/update', [PemohonProfileController::class, 'update'])->name('update');
            Route::get('/change-password', [PemohonProfileController::class, 'editPassword'])->name('change-password');
            Route::put('/update-password', [PemohonProfileController::class, 'updatePassword'])->name('update-password');
        });
    });

    Route::middleware(['admin.role'])->prefix('pemohon')->name('pemohon.')->group(function () {
        Route::get('/', [PemohonController::class, 'index'])->name('index');
        Route::get('/create', [PemohonController::class, 'create'])->name('create');
        Route::post('/', [PemohonController::class, 'store'])->name('store');
        Route::get('/{pemohon}', [PemohonController::class, 'show'])->name('show');
        Route::get('/{pemohon}/edit', [PemohonController::class, 'edit'])->name('edit');
        Route::put('/{pemohon}', [PemohonController::class, 'update'])->name('update');
        Route::patch('/{pemohon}/status', [PemohonController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{pemohon}', [PemohonController::class, 'destroy'])->name('destroy');
    });

    // Pengaduan Routes (Admin) - Use different prefix to avoid conflict
    Route::middleware(['admin.role'])->prefix('admin/pengaduan')->name('admin.pengaduan.')->group(function () {
        Route::get('/', [AdminPengaduanController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminPengaduanController::class, 'show'])->name('show');
        Route::patch('/{id}/update-status', [AdminPengaduanController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{id}', [AdminPengaduanController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/download', [AdminPengaduanController::class, 'download'])->name('download');
    });
});

// Landing page
Route::get('/', [LandingPageController::class, 'index'])->name('landing');

// Layanan page
Route::get('/layanan', [LayananController::class, 'index'])->name('layanan');
Route::get('/layanan/{id}', [LayananController::class, 'show'])->name('layanan.show');

// Informasi page
Route::get('/informasi', [InformasiController::class, 'index'])->name('informasi');
Route::get('/informasi/{slug}', [InformasiController::class, 'show'])->name('informasi.show');

// SKM page
Route::get('/skm', [SkmController::class, 'index'])->name('skm');
Route::post('/skm', [SkmController::class, 'store'])->name('skm.store');
Route::get('/skm/success', [SkmController::class, 'success'])->name('skm.success');
Route::get('/skm/refresh-captcha', [SkmController::class, 'refreshCaptcha'])->name('skm.refresh-captcha');

// Pengaduan page (Front-end)
Route::get('/pengaduan', [FrontPengaduanController::class, 'create'])->name('pengaduan.create');
Route::post('/pengaduan', [FrontPengaduanController::class, 'store'])->name('pengaduan.store');
Route::get('/pengaduan/success', [FrontPengaduanController::class, 'success'])->name('pengaduan.success');
Route::get('/pengaduan/refresh-captcha', [FrontPengaduanController::class, 'refreshCaptcha'])->name('pengaduan.refresh-captcha');
Route::post('/pengaduan/track', [FrontPengaduanController::class, 'track'])->name('pengaduan.track');

// Track Perizinan (Front-end - Public)
Route::post('/perizinan/track', [LandingPageController::class, 'trackPerizinan'])->name('front.perizinan.track');

// Regulasi page (Front-end - Public) - Use different path to avoid conflict with admin
Route::get('/regulasi-public', [FrontRegulasiController::class, 'index'])->name('regulasi.public');
Route::get('/regulasi-public/{id}/download', [FrontRegulasiController::class, 'download'])->name('regulasi.public.download');
