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
use App\Http\Controllers\IzinController;
use App\Http\Controllers\CutiController;

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
| Menonaktifkan register dan reset password dari Laravel default
*/
Auth::routes(['register' => false, 'reset' => false]);

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES - USER/KARYAWAN
|--------------------------------------------------------------------------
| Routes untuk karyawan yang sudah login
*/
Route::middleware(['auth'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | DASHBOARD KARYAWAN
    |----------------------------------------------------------------------
    */
    Route::get('/dashboard', [KaryawanDashboardController::class, 'index'])
        ->name('karyawan.dashboard');
    
    Route::get('/user/dashboard', [KaryawanDashboardController::class, 'index'])
        ->name('user.dashboard');
    
    Route::get('/profile', [KaryawanDashboardController::class, 'profile'])
        ->name('karyawan.profile');

    /*
    |----------------------------------------------------------------------
    | PRESENSI - USER
    |----------------------------------------------------------------------
    */
    Route::prefix('user/presensi')->name('presensi.')->group(function () {
        // Halaman presensi (camera + GPS)
        Route::get('/', [PresensiController::class, 'index'])->name('index');
        Route::get('/create', [PresensiController::class, 'index'])->name('create');
        
        // Store presensi (unified endpoint)
        Route::post('/store', [PresensiController::class, 'store'])->name('store');
        
        // Alternative endpoints (backward compatibility)
        Route::post('/masuk', [PresensiController::class, 'storeMasuk'])->name('masuk');
        Route::post('/keluar', [PresensiController::class, 'storeKeluar'])->name('keluar');
        
        // History & detail
        Route::get('/history', [PresensiController::class, 'history'])->name('history');
        Route::get('/{id}', [PresensiController::class, 'show'])->name('show');
    });

    /*
    |----------------------------------------------------------------------
    | IZIN
    |----------------------------------------------------------------------
    */
    Route::prefix('izin')->name('izin.')->group(function () {
        Route::get('/', [IzinController::class, 'index'])->name('index');
        Route::get('/create', [IzinController::class, 'create'])->name('create');
        Route::post('/', [IzinController::class, 'store'])->name('store');
        Route::get('/{id}', [IzinController::class, 'show'])->name('show');
    });

    /*
    |----------------------------------------------------------------------
    | CUTI
    |----------------------------------------------------------------------
    */
    Route::prefix('cuti')->name('cuti.')->group(function () {
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
| Routes untuk admin/HR yang sudah login
*/
Route::middleware(['auth'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | HOME/DASHBOARD ADMIN
    |----------------------------------------------------------------------
    */
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    /*
    |----------------------------------------------------------------------
    | MASTER DATA
    |----------------------------------------------------------------------
    */
    Route::resource('fakultas', FakultasController::class)
        ->parameters(['fakultas' => 'fakultas']);
    
    Route::resource('departemen', DepartemenController::class);
    
    Route::resource('jabatan', JabatanController::class);
    
    Route::resource('karyawan', KaryawanController::class);
    
    Route::resource('shift', ShiftController::class);
    
    Route::resource('lokasi', LokasiPresensiController::class);

    /*
    |----------------------------------------------------------------------
    | PRESENSI - ADMIN
    |----------------------------------------------------------------------
    */
    Route::prefix('presensi')->group(function () {
        // Admin presensi management
        Route::get('/rekap', [PresensiController::class, 'rekap'])->name('presensi.rekap');
        
        // Legacy routes (if still needed)
        Route::get('/masuk', [PresensiController::class, 'createMasuk'])->name('presensi.createMasuk');
        Route::get('/keluar', [PresensiController::class, 'createKeluar'])->name('presensi.createKeluar');
        Route::post('/masuk', [PresensiController::class, 'storeMasuk'])->name('presensi.storeMasuk');
        Route::post('/keluar', [PresensiController::class, 'storeKeluar'])->name('presensi.storeKeluar');
    });
    
    // Resource route untuk CRUD admin presensi
    Route::resource('presensi', PresensiController::class)
        ->except(['create', 'store']); // exclude karena sudah ada custom routes
});