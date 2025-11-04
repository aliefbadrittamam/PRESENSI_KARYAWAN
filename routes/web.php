<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\CutiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IzinController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\FakultasController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DepartemenController;
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
Route::get('/login/admin', [LoginController::class, 'showAdminLoginForm'])->name('login.admin');
Route::post('/login/admin', [LoginController::class, 'adminLogin'])->name('login.admin.submit');

// 🔹 Login Karyawan
Route::get('/login/karyawan', [LoginController::class, 'showKaryawanLoginForm'])->name('login.karyawan');
Route::post('/login/karyawan', [LoginController::class, 'karyawanLogin'])->name('login.karyawan.submit');

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
    Route::get('/profile', [KaryawanDashboardController::class, 'profile'])->name('karyawan.profile');

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
});

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES - ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // DASHBOARD ADMIN
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // MASTER DATA
    Route::resource('fakultas', FakultasController::class)->parameters(['fakultas' => 'fakultas']);
    Route::resource('departemen', DepartemenController::class);
    Route::resource('jabatan', JabatanController::class);
    Route::resource('karyawan', KaryawanController::class);
    Route::resource('shift', ShiftController::class);
    // Route::resource('lokasi', LokasiPresensiController::class);

    // PRESENSI - ADMIN (PresensiController)
    Route::prefix('presensi')
        ->name('admin.presensi.')
        ->group(function () {
            Route::get('/', [PresensiController::class, 'index'])->name('index');
            Route::get('/rekap', [PresensiController::class, 'rekap'])->name('rekap');
            Route::get('/rekap/download-pdf', [PresensiController::class, 'downloadPdf'])->name('download-pdf');
            Route::get('/{id}', [PresensiController::class, 'show'])->name('show');
        });

    Route::middleware(['auth'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            // ... routes lainnya ...

            // LOKASI PRESENSI - CRUD
           Route::resource('lokasi-presensi', App\Http\Controllers\Admin\LokasiPresensiController::class, [
    'parameters' => ['lokasi-presensi' => 'id']
]);

            // Get coordinates (AJAX)
            Route::post('lokasi-presensi/get-coordinates', [App\Http\Controllers\Admin\LokasiPresensiController::class, 'getCoordinates'])->name('lokasi-presensi.get-coordinates');
        });
});
