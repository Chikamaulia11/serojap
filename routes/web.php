<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    return view('welcome');
});

// Route Dashboard Pelapor
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Jalur Login Pelapor (User)
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

// Jalur Login Admin & Super Admin
Route::get('/login/admin', [AuthenticatedSessionController::class, 'createAdmin'])->name('login.admin');

// Penjaga Pintu Dashboard Admin & Super Admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard'); 
    })->name('admin.dashboard');
    
    // Tambahkan route admin lainnya di sini nanti
});
// Route Profile (Default Laravel Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';