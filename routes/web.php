<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

/* =========================
   LANDING PAGE
========================= */
Route::get('/', function () {
    return view('welcome');
});

/* =========================
   AUTH (GUEST)
========================= */
Route::middleware('guest')->group(function () {

    // LOGIN
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    // REGISTER
    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('/register', [RegisteredUserController::class, 'store']);
});

/* =========================
   LOGOUT
========================= */
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/* =========================
   PROTECTED (PELAPOR)
========================= */
Route::middleware(['auth', 'pelapor'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/report', [ReportController::class, 'create']);
    Route::post('/report', [ReportController::class, 'store']);

    Route::get('/my-report', [ReportController::class, 'index']);

    Route::get('/faq', function () {
        return view('pelapor.faq');
    });

    Route::get('/prosedur', function () {
        return view('pelapor.prosedur');
    });
});