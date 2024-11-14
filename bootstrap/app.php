<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Providers\OpenTelemetryServiceProvider;
use App\Http\Middleware\TraceMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Add the TraceRequest middleware for tracing
        // $middleware->push(\App\Http\Middleware\TraceMiddleware::class);
        $middleware->append(TraceMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Customize exception handling if needed
    })
    ->create();

// Register your OpenTelemetryServiceProvider for tracing
$app->register(OpenTelemetryServiceProvider::class);
