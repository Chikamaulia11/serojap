<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\StatistikController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\AkunAdminController;

// Web Routes

Route::get('/', function () {
    return view('welcome');
});

// LOGIN PELAPOR
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// LOGIN ADMIN
Route::get('/login/admin', [AuthenticatedSessionController::class, 'createAdmin'])->name('login.admin');
Route::post('/login/admin', [AuthenticatedSessionController::class, 'store'])->name('login.admin.post');

// DASHBOARD PELAPOR
Route::get('/dashboard', function () {
    return view('pelapor.dashboard');
})->middleware(['auth', 'verified', 'pelapor'])->name('dashboard');

// PROFILE
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ADMIN ROUTES (auth + admin middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard Admin
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // --- LAPORAN ---
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    // Route::get('/laporan/update-status', [LaporanController::class, 'updateStatusIndex'])->name('laporan.update-status');
    // Route::get('/laporan/riwayat-status', [LaporanController::class, 'riwayatStatusIndex'])->name('laporan.riwayat-status');
    Route::get('/laporan/{id}', [LaporanController::class, 'show'])->name('laporan.show');
    Route::put('/laporan/{id}', [LaporanController::class, 'update'])->name('laporan.update');

    // --- STATISTIK (disembunyikan) ---
    // Route::get('/statistik', [StatistikController::class, 'index'])->name('statistik.index');

    // --- FAQ (disembunyikan) ---
    // Route::get('/faq',           [FaqController::class, 'index'])->name('faq.index');
    // Route::post('/faq',          [FaqController::class, 'store'])->name('faq.store');
    // Route::get('/faq/{id}/edit', [FaqController::class, 'edit'])->name('faq.edit');
    // Route::put('/faq/{id}',      [FaqController::class, 'update'])->name('faq.update');
    // Route::delete('/faq/{id}',   [FaqController::class, 'destroy'])->name('faq.destroy');

    // --- AKUN ADMIN (disembunyikan) ---
    // Route::get('/akun',          [AkunAdminController::class, 'index'])->name('akun.index');
    // Route::post('/akun',         [AkunAdminController::class, 'store'])->name('akun.store');
    // Route::delete('/akun/{id}',  [AkunAdminController::class, 'destroy'])->name('akun.destroy');

});

require __DIR__.'/auth.php';