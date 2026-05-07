<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\LaporanController;

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
    Route::get('/laporan/update-status', [LaporanController::class, 'updateStatusIndex'])->name('laporan.update-status');
    Route::get('/laporan/riwayat-status', [LaporanController::class, 'riwayatStatusIndex'])->name('laporan.riwayat-status');
    Route::get('/laporan/{id}', [LaporanController::class, 'show'])->name('laporan.show');
    Route::put('/laporan/{id}', [LaporanController::class, 'update'])->name('laporan.update');

});

require __DIR__.'/auth.php';