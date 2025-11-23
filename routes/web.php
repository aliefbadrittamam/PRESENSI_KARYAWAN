<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\CutiController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Admin\JabatanController;
use App\Http\Controllers\Admin\FakultasController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\PresensiController;
use App\Http\Controllers\Admin\PengajuanController;
use App\Http\Controllers\Admin\DepartemenController;
use App\Http\Controllers\Admin\FileManagerController;
use App\Http\Controllers\Auth\BarcodeLoginController;
use App\Http\Controllers\Admin\LokasiPresensiController;
use App\Http\Controllers\User\KaryawanPresensiController;
use App\Http\Controllers\User\KaryawanDashboardController;
use App\Http\Controllers\Admin\AdminRekapPresensiController;
use App\Http\Controllers\Admin\PresensiMonitoringController;

// ✅ NEW: Import controllers untuk Izin
use App\Http\Controllers\User\IzinController as UserIzinController;
use App\Http\Controllers\Admin\IzinController as AdminIzinController;

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
    
    // ============================================
    // DASHBOARD
    // ============================================
    Route::get('/dashboard', [KaryawanDashboardController::class, 'index'])
        ->name('karyawan.dashboard');
    Route::get('/user/dashboard', [KaryawanDashboardController::class, 'index'])
        ->name('user.dashboard');

    // ============================================
    // PRESENSI - USER
    // ============================================
    Route::prefix('user/presensi')->name('presensi.')->group(function () {
        Route::get('/', [KaryawanPresensiController::class, 'index'])->name('index');
        Route::get('/create', [KaryawanPresensiController::class, 'index'])->name('create');
        Route::get('/history', [KaryawanPresensiController::class, 'history'])->name('history');
        Route::get('/rekap', [KaryawanPresensiController::class, 'rekap'])->name('rekap');
        Route::get('/{id}', [KaryawanPresensiController::class, 'show'])->name('show');
        
        Route::post('/store', [KaryawanPresensiController::class, 'store'])->name('store');
        Route::post('/masuk', [KaryawanPresensiController::class, 'storeMasuk'])->name('masuk');
        Route::post('/keluar', [KaryawanPresensiController::class, 'storeKeluar'])->name('keluar');
    });

    // ============================================
    // IZIN - USER
    // ============================================
    Route::prefix('user/izin')->name('user.izin.')->group(function () {
        Route::get('/', [UserIzinController::class, 'index'])->name('index');
        Route::get('/create', [UserIzinController::class, 'create'])->name('create');
        Route::get('/{id}', [UserIzinController::class, 'show'])->name('show');
        
        Route::post('/', [UserIzinController::class, 'store'])->name('store');
    });

    // ============================================
    // CUTI - USER
    // ============================================
    Route::prefix('user/cuti')->name('user.cuti.')->group(function () {
        Route::get('/', [CutiController::class, 'index'])->name('index');
        Route::get('/create', [CutiController::class, 'create'])->name('create');
        Route::get('/{id}', [CutiController::class, 'show'])->name('show');
        
        Route::post('/', [CutiController::class, 'store'])->name('store');
    });

    // ============================================
    // PROFILE
    // ============================================
    Route::prefix('user/profile')->name('karyawan.')->group(function () {
        // Read
        Route::get('/', [ProfileController::class, 'index'])->name('profile');
        
        // Write/Update
        Route::post('/create-password', [ProfileController::class, 'createPassword'])->name('create-password');
        Route::post('/update-password', [ProfileController::class, 'updatePassword'])->name('update-password');
        Route::post('/update-photo', [ProfileController::class, 'updatePhoto'])->name('update-photo');
        Route::post('/regenerate-qrcode', [ProfileController::class, 'regenerateQRCode'])->name('regenerate-qrcode');
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

        // ✅ CHANGE PASSWORD ROUTES
        Route::get('karyawan/{karyawan}/change-password', [KaryawanController::class, 'showChangePasswordForm'])->name('karyawan.change-password');
        Route::post('karyawan/{karyawan}/change-password', [KaryawanController::class, 'changePassword'])->name('karyawan.update-password');

        Route::resource('karyawan', KaryawanController::class);
        Route::resource('shift', ShiftController::class);

        // PRESENSI MONITORING
        Route::get('presensi/monitoring', [PresensiMonitoringController::class, 'index'])->name('presensi.monitoring');
        Route::get('presensi/monitoring/export/excel', [PresensiMonitoringController::class, 'exportExcel'])->name('presensi.monitoring.export-excel');
        Route::get('presensi/monitoring/export/pdf', [PresensiMonitoringController::class, 'exportPDF'])->name('presensi.monitoring.export-pdf');
        Route::get('presensi/monitoring/{id}', [PresensiMonitoringController::class, 'show'])->name('presensi.monitoring.show');

        // PRESENSI - ADMIN (PresensiController)
        Route::prefix('presensi')
            ->name('presensi.')
            ->group(function () {
                Route::get('/rekap', [AdminRekapPresensiController::class, 'index'])->name('rekap');
                Route::get('/rekap/download-pdf', [AdminRekapPresensiController::class, 'downloadPdf'])->name('download-pdf');

                Route::get('/{id}', [PresensiController::class, 'show'])->name('show');
            });

        // File Manager
        Route::get('/file-manager', [FileManagerController::class, 'index'])->name('file-manager.index');
        Route::get('/file-manager/show/{path}', [FileManagerController::class, 'show'])
            ->name('file-manager.show')
            ->where('path', '.*');
        Route::get('/file-manager/download/{path}', [FileManagerController::class, 'download'])
            ->name('file-manager.download')
            ->where('path', '.*');
        Route::delete('/file-manager/delete', [FileManagerController::class, 'delete'])->name('file-manager.delete');
        Route::post('/file-manager/bulk-delete', [FileManagerController::class, 'bulkDelete'])->name('file-manager.bulk-delete');

        // ✅ UPDATED: PENGAJUAN (IZIN & CUTI) - Menggunakan PengajuanController yang sudah ada
        Route::prefix('pengajuan')
            ->name('pengajuan.')
            ->group(function () {
                // List all pengajuan
                Route::get('/', [PengajuanController::class, 'index'])->name('index');

                // Detail
                Route::get('/izin/{id}', [PengajuanController::class, 'showIzin'])->name('show-izin');
                Route::get('/cuti/{id}', [PengajuanController::class, 'showCuti'])->name('show-cuti');

                // Approve
                Route::post('/izin/{id}/approve', [PengajuanController::class, 'approveIzin'])->name('approve-izin');
                Route::post('/cuti/{id}/approve', [PengajuanController::class, 'approveCuti'])->name('approve-cuti');

                // Reject
                Route::post('/izin/{id}/reject', [PengajuanController::class, 'rejectIzin'])->name('reject-izin');
                Route::post('/cuti/{id}/reject', [PengajuanController::class, 'rejectCuti'])->name('reject-cuti');
            });

        // ✅ NEW: IZIN MANAGEMENT (Alternative/Dedicated Routes - Optional)
        // Hanya digunakan jika ingin route terpisah dari PengajuanController
        // Uncomment jika ingin route dedicated untuk izin
        /*
        Route::prefix('izin')
            ->name('izin.')
            ->group(function () {
                // List all izin submissions
                Route::get('/', [AdminIzinController::class, 'index'])->name('index');
                
                // View izin detail
                Route::get('/{id}', [AdminIzinController::class, 'show'])->name('show');
                
                // Approve izin
                Route::post('/{id}/approve', [AdminIzinController::class, 'approve'])->name('approve');
                
                // Reject izin
                Route::post('/{id}/reject', [AdminIzinController::class, 'reject'])->name('reject');
                
                // Reject dengan soft delete (alternative)
                Route::post('/{id}/reject-soft', [AdminIzinController::class, 'rejectSoftDelete'])->name('reject.soft');
                
                // Utility: Reprocess presensi
                Route::post('/{id}/reprocess', [AdminIzinController::class, 'reprocessPresensi'])->name('reprocess');
                
                // Bulk approve
                Route::post('/bulk-approve', [AdminIzinController::class, 'bulkApprove'])->name('bulk.approve');
                
                // Bulk reject
                Route::post('/bulk-reject', [AdminIzinController::class, 'bulkReject'])->name('bulk.reject');
            });
        */

        // LOKASI PRESENSI - CRUD (Explicit Routes)
        Route::prefix('lokasi-presensi')
            ->name('lokasi-presensi.')
            ->group(function () {
                // List
                Route::get('/', [LokasiPresensiController::class, 'index'])->name('index');

                // Create
                Route::get('/create', [LokasiPresensiController::class, 'create'])->name('create');
                Route::post('/', [LokasiPresensiController::class, 'store'])->name('store');

                // Edit
                Route::get('/{id}/edit', [LokasiPresensiController::class, 'edit'])->name('edit');
                Route::put('/{id}', [LokasiPresensiController::class, 'update'])->name('update');

                // Show
                Route::get('/{id}', [LokasiPresensiController::class, 'show'])->name('show');

                // Delete
                Route::delete('/{id}', [LokasiPresensiController::class, 'destroy'])->name('destroy');

                // Get coordinates (AJAX)
                Route::post('/get-coordinates', [LokasiPresensiController::class, 'getCoordinates'])->name('get-coordinates');
            });
    });

// ✅ BACKWARD COMPATIBILITY: Route untuk change password (duplicate, tapi tetap ada untuk compatibility)
Route::middleware(['auth'])->group(function () {
    // Form ubah password
    Route::get('/admin/karyawan/{karyawan}/change-password', [KaryawanController::class, 'showChangePasswordForm'])->name('admin.karyawan.change-password');

    // Proses ubah password
    Route::post('/admin/karyawan/{karyawan}/change-password', [KaryawanController::class, 'changePassword'])->name('admin.karyawan.update-password');
});