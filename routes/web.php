<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\FakultasController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\LokasiPresensiController;
use App\Http\Controllers\Auth\BarcodeLoginController;
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
Route::get('/barcode-login/{token}', [BarcodeLoginController::class, 'login'])->name('barcode.login');
Route::get('/barcode-scanner', [BarcodeLoginController::class, 'scanner'])->name('barcode.scanner');
                           

Route::get('/user/dashboard', [KaryawanDashboardController::class, 'index'])
    ->middleware('auth')
    ->name('user.dashboard');

// 🔹 Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| NON-REGISTERED AUTH ROUTES
|--------------------------------------------------------------------------
|
| Menonaktifkan register dan reset password dari Laravel default
|
*/
Auth::routes(['register' => false, 'reset' => false]);

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (HANYA UNTUK USER LOGIN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:karyawan')->group(function () {
    Route::get('presensi/masuk', [PresensiController::class, 'createMasuk'])->name('presensi.masuk');
});

Route::middleware(['auth'])->group(function () {

    // 🔹 Halaman utama (Dashboard)
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    /*
    |--------------------------------------------------------------------------
    | MASTER DATA
    |--------------------------------------------------------------------------
    */
    Route::resource('fakultas', FakultasController::class)->parameters(['fakultas' => 'fakultas']);
    Route::resource('departemen', DepartemenController::class);
    Route::resource('jabatan', JabatanController::class);
    Route::resource('karyawan', KaryawanController::class);
    Route::resource('shift', ShiftController::class);
    Route::resource('lokasi', LokasiPresensiController::class);

    /*
    |--------------------------------------------------------------------------
    | PRESENSI ROUTES
    |--------------------------------------------------------------------------
    */
    Route::resource('presensi', PresensiController::class);
    Route::get('presensi/rekap', [PresensiController::class, 'rekap'])->name('presensi.rekap');
    Route::get('presensi/masuk', [PresensiController::class, 'createMasuk'])->name('presensi.masuk');
    Route::post('presensi/masuk', [PresensiController::class, 'storeMasuk'])->name('presensi.storeMasuk');
    Route::get('presensi/keluar', [PresensiController::class, 'createKeluar'])->name('presensi.keluar');
    Route::post('presensi/keluar', [PresensiController::class, 'storeKeluar'])->name('presensi.storeKeluar');
});

Route::middleware(['auth'])->group(function () {
    
    // Dashboard Karyawan
    Route::get('/dashboard', [KaryawanDashboardController::class, 'index'])
        ->name('karyawan.dashboard');
    
    Route::get('/profile', [KaryawanDashboardController::class, 'profile'])
        ->name('karyawan.profile');
    
    // Presensi Routes
    Route::prefix('presensi')->group(function () {
        Route::get('/', [PresensiController::class, 'index'])->name('presensi.index');
        Route::get('/create', [PresensiController::class, 'create'])->name('presensi.create');
        Route::post('/masuk', [PresensiController::class, 'storeMasuk'])->name('presensi.masuk');
        Route::post('/keluar', [PresensiController::class, 'storeKeluar'])->name('presensi.keluar');
        Route::get('/history', [PresensiController::class, 'history'])->name('presensi.history');
        Route::get('/{id}', [PresensiController::class, 'show'])->name('presensi.show');
    });
    
    // Izin Routes
    Route::prefix('izin')->group(function () {
        Route::get('/', [IzinController::class, 'index'])->name('izin.index');
        Route::get('/create', [IzinController::class, 'create'])->name('izin.create');
        Route::post('/', [IzinController::class, 'store'])->name('izin.store');
        Route::get('/{id}', [IzinController::class, 'show'])->name('izin.show');
    });
    
    // Cuti Routes
    Route::prefix('cuti')->group(function () {
        Route::get('/', [CutiController::class, 'index'])->name('cuti.index');
        Route::get('/create', [CutiController::class, 'create'])->name('cuti.create');
        Route::post('/', [CutiController::class, 'store'])->name('cuti.store');
        Route::get('/{id}', [CutiController::class, 'show'])->name('cuti.show');
    });
    
});
