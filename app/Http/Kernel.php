<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use OpenTelemetry\API\Trace\Tracer;
use OpenTelemetry\Context\Context;
use Illuminate\Http\Request;
use App\Providers\OpenTelemetryServiceProvider;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\PreventRequestsFromSuspiciousSources::class,
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    
    protected $middlewareGroups = [
        'web' => [
           
            \App\Http\Middleware\TraceMiddleware::class,  // Add middleware for tracing
        ],

        'api' => [
            \App\Http\Middleware\TraceMiddleware::class
        ],
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'trace' => \App\Http\Middleware\TraceMiddleware::class, // Add trace middleware here
    ];
}
