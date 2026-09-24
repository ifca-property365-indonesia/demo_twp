<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin-auth'  => \App\Http\Middleware\AdminAuth::class,
            'tenant-auth' => \App\Http\Middleware\TenantAuth::class,
            'revalidate'  => \App\Http\Middleware\RevalidateBackHistory::class,
        ]);

        // Bahasa pilihan user (session / cookie 'locale') untuk semua halaman web.
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
    })->create();