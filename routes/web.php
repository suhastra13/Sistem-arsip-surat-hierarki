<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\DisposisiController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect('/login');
});

// Route untuk Tamu
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Route untuk Member
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [LaporanController::class, 'index'])->name('dashboard');
    // Route Surat Masuk
    Route::resource('surat-masuk', SuratMasukController::class);
    // Route Disposisi (Inbox & Proses)
    Route::resource('disposisi', DisposisiController::class)->only(['index', 'show', 'update']);
    // Route Surat Keluar
    Route::resource('surat-keluar', SuratKeluarController::class);
    // Route Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::resource('users', UserController::class);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
