<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('pelapor.dashboard');
});

Route::get('/dashboard', function () {
    return view('pelapor.dashboard');
});

Route::get('/report', function () {
    return view('pelapor.form');
});

Route::get('/my-report', function () {
    return view('pelapor.riwayat');
});