<?php

use App\Http\Controllers\AuthController;
use App\Http\Middlewares\SessionAlreadyActiveMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('auth')->group(function () {
    Route::post('/login',       [AuthController::class, 'login'])->name('login');
    Route::post('/logout',      [AuthController::class, 'logout'])->name('logout');
    Route::post('/register',    [AuthController::class, 'register'])->name('register');
    Route::get('/login',        [AuthController::class, 'showLogin'])->name('login-form');
})->middleware(SessionAlreadyActiveMiddleware::class);

