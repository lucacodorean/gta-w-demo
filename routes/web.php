<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Middlewares\EnsureNotSessionActiveMiddleware;
use App\Http\Middlewares\EnsureSessionActiveMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('auth')->group(function () {
    Route::post('/login',       [AuthController::class, 'login'])->name('login');
    Route::post('/logout',      [AuthController::class, 'logout'])->name('logout');
    Route::post('/register',    [AuthController::class, 'register'])->name('register');
    Route::get('/register',     [AuthController::class, 'showRegister'])->name('register-form');
    Route::get('/login',        [AuthController::class, 'showLogin'])->name('login-form');
})->middleware(EnsureNotSessionActiveMiddleware::class);

Route::prefix('notes')->group(function () {
    Route::get('/', [DashboardController::class, 'showHome'])->name('home');
})->middleware(EnsureSessionActiveMiddleware::class);
