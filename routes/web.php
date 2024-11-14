<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\TraceMiddleware;
use OpenTelemetry\API\Trace\TracerInterface;

Route::get('/', function () {
    error_log("comming here rehehhdkshfldksh");
    return view('welcome');
});


Route::get('login', [AuthController::class, 'login_view'],)->middleware(TraceMiddleware::class)->name('login');
Route::post('login', [AuthController::class, 'login'])->middleware(TraceMiddleware::class)->name('login');

Route::get('register', [AuthController::class, 'register_view'])->middleware(TraceMiddleware::class)->name("register");
Route::post('register', [AuthController::class, 'register'])->middleware(TraceMiddleware::class)->name("register");
