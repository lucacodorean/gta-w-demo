<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->middleware('guest')->group(function () {
    Route::post('/login',       [AuthController::class, 'login'])->name('login');
    Route::post('/register',    [AuthController::class, 'register'])->name('register');
    Route::get('/register',     [AuthController::class, 'showRegister'])->name('register-form');
    Route::get('/login',        [AuthController::class, 'showLogin'])->name('login-form');
});

Route::prefix('auth')->middleware('auth')->group(function () {
    Route::post('/logout',      [AuthController::class, 'logout'])->name('logout');
});

Route::prefix('notes')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'showHome'])->name('home');
});
