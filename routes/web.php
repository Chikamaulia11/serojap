<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| WEB ROUTES - SEROJAP
|--------------------------------------------------------------------------
*/

/* =========================
   🔁 DEFAULT REDIRECT
========================= */
Route::get('/', function () {
    return redirect('/login');
});

/* =========================
   🔐 AUTH (GUEST ONLY)
========================= */
Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register']);

});

/* =========================
   🔒 AUTH + ROLE PELAPOR
========================= */
Route::middleware(['auth', 'pelapor'])->group(function () {

    /* DASHBOARD */
    Route::get('/dashboard', [DashboardController::class, 'index']);

    /* FORM LAPORAN */
    Route::get('/report', [ReportController::class, 'create']);
    Route::post('/report', [ReportController::class, 'store']);

    /* RIWAYAT */
    Route::get('/my-report', [ReportController::class, 'index']);

    /* FAQ (sementara view langsung) */
    Route::get('/faq', function () {
        return view('pelapor.faq');
    });

    /* PROSEDUR */
    Route::get('/prosedur', function () {
        return view('pelapor.prosedur');
    });

    /* LOGOUT */
    Route::post('/logout', [AuthController::class, 'logout']);

});