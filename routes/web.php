<?php

use Illuminate\Support\Facades\Route;
use OpenTelemetry\API\Trace\TracerInterface;
use App\Http\Controllers\AuthController;
use function App\Services\traceRoute;
require_once __DIR__ . '/../services/traceRouteFile.php';

Route::get('/', function (TracerInterface $tracer) {
    return traceRoute('welcome', '/', 'get', function() {
        return view('welcome');
    }, $tracer);
});

Route::post('login', function (TracerInterface $tracer) {
    return traceRoute('login', 'login', 'post', function() {
        return app(AuthController::class)->login(request());
    }, $tracer);
})->name('login');

Route::get('login', function (TracerInterface $tracer) {
    return traceRoute('login', 'login', 'get', function() {
        return app(AuthController::class)->login(request());
    }, $tracer);
})->name('login');

Route::get('register', function (TracerInterface $tracer) {
    return traceRoute('register', 'register', 'get', function() {
        return app(AuthController::class)->register_view(request());
    }, $tracer);
})->name('register');

Route::post('register', function (TracerInterface $tracer) {
    return traceRoute('register', 'register', 'post', function() use ($tracer) {
        return app(AuthController::class)->register(request(),$tracer);
    }, $tracer);
})->name('register');
