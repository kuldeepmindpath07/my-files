<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('login',[AuthController::class,'index'])->name('login');
Route::get('login',[AuthController::class,'login'])->name('login');
Route::get('register',[AuthController::class,'register_view'])->name('register');
Route::post('register',[AuthController::class,'register'])->name('register');