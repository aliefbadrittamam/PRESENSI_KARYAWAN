<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\JabatanController;
use App\Http\Controllers\Admin\FakultasController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\PresensiController;
use App\Http\Controllers\User\CutiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DepartemenController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Auth\BarcodeLoginController;
use App\Http\Controllers\Admin\LokasiPresensiController;
use App\Http\Controllers\User\KaryawanPresensiController;
use App\Http\Controllers\User\KaryawanDashboardController;

/*
|--------------------------------------------------------------------------
| Redirect Root ke Login Karyawan
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login.karyawan');
});

/*
|--------------------------------------------------------------------------
| LOGIN ROUTES (ADMIN & KARYAWAN)
|--------------------------------------------------------------------------
*/

// 🔹 Login Admin
Route::middleware('redirect.role')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login.karyawan');
    });

    // Login Admin
    Route::get('/login/admin', [LoginController::class, 'showAdminLoginForm'])->name('login.admin');
    Route::post('/login/admin', [LoginController::class, 'adminLogin'])->name('login.admin.submit');

    // Login Karyawan
    Route::get('/login/karyawan', [LoginController::class, 'showKaryawanLoginForm'])->name('login.karyawan');
    Route::post('/login/karyawan', [LoginController::class, 'karyawanLogin'])->name('login.karyawan.submit');
});

// 🔹 Barcode Login
Route::get('/barcode-login/{token}', [BarcodeLoginController::class, 'login'])->name('barcode.login');
Route::get('/barcode-scanner', [BarcodeLoginController::class, 'scanner'])->name('barcode.scanner');

// 🔹 Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| NON-REGISTERED AUTH ROUTES
|--------------------------------------------------------------------------
*/
Auth::routes(['register' => false, 'reset' => false]);

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES - USER/KARYAWAN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // DASHBOARD KARYAWAN
    Route::get('/dashboard', [KaryawanDashboardController::class, 'index'])->name('karyawan.dashboard');
    Route::get('/user/dashboard', [KaryawanDashboardController::class, 'index'])->name('user.dashboard');

    // PRESENSI - USER (KaryawanPresensiController)
    Route::prefix('user/presensi')
        ->name('presensi.')
        ->group(function () {
            Route::get('/', [KaryawanPresensiController::class, 'index'])->name('index');
            Route::get('/create', [KaryawanPresensiController::class, 'index'])->name('create');

            Route::post('/store', [KaryawanPresensiController::class, 'store'])->name('store');
            Route::post('/masuk', [KaryawanPresensiController::class, 'storeMasuk'])->name('masuk');
            Route::post('/keluar', [KaryawanPresensiController::class, 'storeKeluar'])->name('keluar');

            Route::get('/history', [KaryawanPresensiController::class, 'history'])->name('history');
            Route::get('/rekap', [KaryawanPresensiController::class, 'rekap'])->name('rekap');

            Route::get('/{id}', [KaryawanPresensiController::class, 'show'])->name('show');
        });

    // IZIN
    Route::prefix('izin')
        ->name('izin.')
        ->group(function () {
            Route::get('/', [IzinController::class, 'index'])->name('index');
            Route::get('/create', [IzinController::class, 'create'])->name('create');
            Route::post('/', [IzinController::class, 'store'])->name('store');
            Route::get('/{id}', [IzinController::class, 'show'])->name('show');
        });

    // CUTI
    Route::prefix('cuti')
        ->name('cuti.')
        ->group(function () {
            Route::get('/', [CutiController::class, 'index'])->name('index');
            Route::get('/create', [CutiController::class, 'create'])->name('create');
            Route::post('/', [CutiController::class, 'store'])->name('store');
            Route::get('/{id}', [CutiController::class, 'show'])->name('show');
        });

    // PROFILE
    Route::prefix('profile')
        ->name('karyawan.')
        ->group(function () {
            // Display profile
            Route::get('/', [ProfileController::class, 'index'])->name('profile');

            // Create Password (untuk user yang belum punya password)
            Route::post('/create-password', [ProfileController::class, 'createPassword'])->name('create-password');

            // Update Password (untuk user yang sudah punya password)
            Route::post('/update-password', [ProfileController::class, 'updatePassword'])->name('update-password');

            // Update Photo
            Route::post('/update-photo', [ProfileController::class, 'updatePhoto'])->name('update-photo');

            // Regenerate QR Code
            Route::post('/regenerate-qrcode', [ProfileController::class, 'regenerateQRCode'])->name('regenerate-qrcode');

            // Update Profile Info (optional - jika mau edit email/phone)
            Route::post('/update-info', [ProfileController::class, 'updateProfile'])->name('update-info');
        });
});

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES - ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // DASHBOARD ADMIN
        Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

        // Redirect /home ke /admin/dashboard untuk backward compatibility
        Route::redirect('/home', '/admin/dashboard');

        // MASTER DATA
        Route::resource('fakultas', FakultasController::class)->parameters(['fakultas' => 'fakultas']);
        Route::resource('departemen', DepartemenController::class)->parameters(['departemen' => 'departemen']);
        Route::resource('jabatan', JabatanController::class);
        Route::resource('karyawan', KaryawanController::class);
        Route::resource('shift', ShiftController::class);

        Route::get('presensi/monitoring', [App\Http\Controllers\Admin\PresensiMonitoringController::class, 'index'])->name('presensi.monitoring');
        Route::get('presensi/monitoring/export/excel', [App\Http\Controllers\Admin\PresensiMonitoringController::class, 'exportExcel'])->name('presensi.monitoring.export-excel');
        Route::get('presensi/monitoring/export/pdf', [App\Http\Controllers\Admin\PresensiMonitoringController::class, 'exportPDF'])->name('presensi.monitoring.export-pdf');
        Route::get('presensi/monitoring/{id}', [App\Http\Controllers\Admin\PresensiMonitoringController::class, 'show'])->name('presensi.monitoring.show');

        // PRESENSI - ADMIN (PresensiController)
        Route::prefix('presensi')
            ->name('presensi.')
            ->group(function () {
                
                // ========================================
                // 🔥 PERBAIKAN: Gunakan AdminRekapPresensiController
                // ========================================
                Route::get('/rekap', [App\Http\Controllers\Admin\AdminRekapPresensiController::class, 'index'])->name('rekap');
                Route::get('/rekap/download-pdf', [App\Http\Controllers\Admin\AdminRekapPresensiController::class, 'downloadPdf'])->name('download-pdf');
                
                Route::get('/{id}', [PresensiController::class, 'show'])->name('show');
            });

        // File Manager
        Route::get('/file-manager', [App\Http\Controllers\Admin\FileManagerController::class, 'index'])->name('file-manager.index');
        Route::get('/file-manager/show/{path}', [App\Http\Controllers\Admin\FileManagerController::class, 'show'])
            ->name('file-manager.show')
            ->where('path', '.*');
        Route::get('/file-manager/download/{path}', [App\Http\Controllers\Admin\FileManagerController::class, 'download'])
            ->name('file-manager.download')
            ->where('path', '.*');
        Route::delete('/file-manager/delete', [App\Http\Controllers\Admin\FileManagerController::class, 'delete'])->name('file-manager.delete');
        Route::post('/file-manager/bulk-delete', [App\Http\Controllers\Admin\FileManagerController::class, 'bulkDelete'])->name('file-manager.bulk-delete');
        
        // PENGAJUAN (IZIN & CUTI)
        Route::prefix('pengajuan')
            ->name('pengajuan.')
            ->group(function () {
                // List all pengajuan
                Route::get('/', [App\Http\Controllers\Admin\PengajuanController::class, 'index'])->name('index');

                // Detail
                Route::get('/izin/{id}', [App\Http\Controllers\Admin\PengajuanController::class, 'showIzin'])->name('show-izin');
                Route::get('/cuti/{id}', [App\Http\Controllers\Admin\PengajuanController::class, 'showCuti'])->name('show-cuti');

                // Approve
                Route::post('/izin/{id}/approve', [App\Http\Controllers\Admin\PengajuanController::class, 'approveIzin'])->name('approve-izin');
                Route::post('/cuti/{id}/approve', [App\Http\Controllers\Admin\PengajuanController::class, 'approveCuti'])->name('approve-cuti');

                // Reject
                Route::post('/izin/{id}/reject', [App\Http\Controllers\Admin\PengajuanController::class, 'rejectIzin'])->name('reject-izin');
                Route::post('/cuti/{id}/reject', [App\Http\Controllers\Admin\PengajuanController::class, 'rejectCuti'])->name('reject-cuti');
            });

        // LOKASI PRESENSI - CRUD
        Route::resource('lokasi-presensi', App\Http\Controllers\Admin\LokasiPresensiController::class, [
            'parameters' => ['lokasi-presensi' => 'id'],
        ]);

        // Get coordinates (AJAX)
        Route::post('lokasi-presensi/get-coordinates', [App\Http\Controllers\Admin\LokasiPresensiController::class, 'getCoordinates'])->name('lokasi-presensi.get-coordinates');
    });