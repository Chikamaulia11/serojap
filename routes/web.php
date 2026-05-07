<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\StatistikController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\AkunAdminController;
use App\Http\Controllers\PelaporController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('pelapor.dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// PELAPOR ROUTES
Route::middleware(['auth'])->prefix('pelapor')->name('pelapor.')->group(function () {
    Route::get('/dashboard', [PelaporController::class, 'dashboard'])->name('dashboard');
    Route::get('/laporan/create', [PelaporController::class, 'create'])->name('laporan.create');
    Route::post('/laporan', [PelaporController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/{id}', [PelaporController::class, 'show'])->name('laporan.show');
});

require __DIR__.'/auth.php';

/* ADMIN ROUTES */
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/update-status', [LaporanController::class, 'updateStatusIndex'])->name('laporan.update-status');
    Route::get('/laporan/riwayat-status', [LaporanController::class, 'riwayatStatusIndex'])->name('laporan.riwayat-status');
    Route::get('/laporan/{id}', [LaporanController::class, 'show'])->name('laporan.show');
    Route::put('/laporan/{id}', [LaporanController::class, 'update'])->name('laporan.update');

    Route::get('/statistik', function() {
        return view('admin.statistik');
    })->name('statistik.index');
});

