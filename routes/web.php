<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| WEB ROUTES - SEROJAP
|--------------------------------------------------------------------------
*/

/* =========================
   DEFAULT REDIRECT
========================= */
Route::get('/', function () {
    return redirect('/dashboard');
});

/* =========================
   AUTH BAWAAN LARAVEL
========================= */
require __DIR__.'/auth.php';

/* =========================
   AUTH + ROLE PELAPOR
========================= */
Route::middleware(['auth', 'pelapor'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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