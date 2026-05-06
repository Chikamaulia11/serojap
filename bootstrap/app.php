<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
<<<<<<< HEAD
$middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'pelapor' => \App\Http\Middleware\PelaporMiddleware::class,
=======

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'pelapor' => \App\Http\Middleware\PelaporMiddleware::class, // 🔥 TAMBAHAN WAJIB
>>>>>>> b793b33 (backup sebelum merge)
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();