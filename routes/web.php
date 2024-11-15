<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\TraceMiddleware;

Route::get('/', function () {
    return view('welcome');
});

    Route::get('login', [AuthController::class, 'login_view'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login');

    Route::get('register', [AuthController::class, 'register_view'])->name('register');
    Route::post('register', [AuthController::class, 'register'])->name('register');
