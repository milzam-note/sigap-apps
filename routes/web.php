<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// 1. Route Publik (Tanpa Login)
Route::get('/', [SuratController::class, 'publicIndex'])->name('home');
Route::get('/surat/{id}/lihat', [SuratController::class, 'lihat'])->name('surat.lihat');
Route::get('/surat/{id}/unduh', [SuratController::class, 'unduh'])->name('surat.unduh');

// Route Riwayat surat
Route::get('/riwayat/{id}/lihat', [SuratController::class, 'lihatRiwayat'])->name('riwayat.lihat');
Route::get('/riwayat/{id}/unduh', [SuratController::class, 'unduhRiwayat'])->name('riwayat.unduh');

// 2. Route Internal & Admin (Wajib Login & Terverifikasi)
Route::middleware(['auth', 'verified'])->group(function () {

    // Route Dashboard Baru menggunakan Controller
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Route Profile Bawaan Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route Kelola Surat
    Route::get('/surat', [SuratController::class, 'index'])->name('surat.index');
    Route::post('/surat', [SuratController::class, 'store'])->name('surat.store');
    Route::put('/surat/{id}', [SuratController::class, 'update'])->name('surat.update');
    Route::delete('/surat/{id}', [SuratController::class, 'destroy'])->name('surat.destroy');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::put('/users/{id}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');
    Route::put('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
});

require __DIR__ . '/auth.php';
