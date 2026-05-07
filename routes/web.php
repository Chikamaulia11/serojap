<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\TabelFaqController;

Route::get('/', function () { return view('welcome'); });

// DASHBOARD PELAPOR
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// LOGIN PELAPOR
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// LOGIN ADMIN
Route::get('/login/admin', [AuthenticatedSessionController::class, 'createAdmin'])->name('login.admin');
Route::post('/login/admin', [AuthenticatedSessionController::class, 'store'])->name('login.admin.post');

// DASHBOARD ADMIN
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard'); 
    })->name('admin.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Ini route punya kamu sendiri (Lanjut kerjakan yang ini)
    Route::resource('manajemen-faq', TabelFaqController::class);

    // INI ROUTE PANCINGAN (Biar nggak error pas dipanggil sidebar)
    // Nanti kalau sudah di-merge, bagian ini tinggal dihapus
    Route::get('/laporan-pancingan', function() {
        return "Modul ini sedang dikerjakan tim lain dan belum di-merge.";
    })->name('laporan.index');

    // Tambahkan pancingan lain jika ada error route lain
    Route::get('/dashboard', function () { return view('admin.dashboard'); })->name('dashboard');
});

require __DIR__.'/auth.php';