<?php

use App\Http\Controllers\FakultasController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\KaryawanController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Resource Routes
Route::resource('fakultas', FakultasController::class);
Route::resource('departemen', DepartemenController::class);
Route::resource('jabatan', JabatanController::class);
Route::resource('karyawan', KaryawanController::class);