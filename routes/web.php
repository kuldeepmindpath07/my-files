<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use OpenTelemetry\API\Trace\TracerInterface;

Route::get('/', function (TracerInterface $tracer) {
    $span = $tracer->spanBuilder('route.welcome')->startSpan();
    $scope = $span->activate();

    try {
        $span->setAttribute('http.route', '/');
        $span->setAttribute('http.method', 'GET');
        return view('welcome');
    } finally {
        $span->end();
        $scope->detach();  // Ensure the scope is detached here
    }
});

Route::post('login', function (TracerInterface $tracer) {
    $span = $tracer->spanBuilder('route.login.post')->startSpan();
    $scope = $span->activate();

    try {
        $span->setAttribute('http.route', 'login');
        $span->setAttribute('http.method', 'POST');
        return app(AuthController::class)->login(request());  // Ensure this points to the correct controller method
    } finally {
        $span->end();
        $scope->detach();
    }
})->name('login');

Route::get('login', function (TracerInterface $tracer) {
    $myspan = $tracer->spanBuilder('route.login.get')->startSpan();
    $myscope = $myspan->activate();

    try {
        $myspan->setAttribute('http.route', 'login');
        $myspan->setAttribute('http.method', 'GET');
        error_log("commming comming 1");
        return app(AuthController::class)->login(request());  // Ensure this points to the correct controller method
    } finally {
        error_log("commming comming 2");
        $myspan->end();
        error_log("commming comming 3");
        $myscope->detach();
        error_log("commming comming 4");
    }
})->name('login');

Route::get('register', function (TracerInterface $tracer) {
    $span = $tracer->spanBuilder('route.register.get')->startSpan();
    $scope = $span->activate();

    try {
        $span->setAttribute('http.route', 'register');
        $span->setAttribute('http.method', 'GET');
        return app(AuthController::class)->register_view(request());  // Ensure this points to the correct controller method
    } finally {
        $span->end();
        $scope->detach();
    }
})->name('register');

Route::post('register', function (TracerInterface $tracer) {
    $span = $tracer->spanBuilder('route.register.post')->startSpan();
    $scope = $span->activate();

    try {
        $span->setAttribute('http.route', 'register');
        $span->setAttribute('http.method', 'POST');
        
        // Call the controller method
        return app(AuthController::class)->register(request(), $tracer);
    } finally {
        $span->end();
        $scope->detach();
    }
})->name('register');

