<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Providers\OpenTelemetryServiceProvider;
use App\Http\Middleware\TraceMiddleware;
use App\Http\Middleware\LogRequestDetails;
use App\Http\Middleware\TraceLoggingMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(TraceMiddleware::class);
        $middleware->append(LogRequestDetails::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Customize exception handling if needed
    })
    ->create();

// Register your OpenTelemetryServiceProvider for tracing
$app->register(OpenTelemetryServiceProvider::class);
