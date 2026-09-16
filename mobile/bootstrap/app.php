<?php

use App\Http\Middleware\EnsureManagementRole;
use App\Http\Middleware\EnsureMobileAuthenticated;
use App\Http\Middleware\EnsureResidentRole;
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
        $middleware->alias([
            'mobile.auth' => EnsureMobileAuthenticated::class,
            'mobile.management' => EnsureManagementRole::class,
            'mobile.resident' => EnsureResidentRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
