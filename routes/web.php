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
    Route::post('/', [DashboardController::class, 'createNote'])->name('create-note');
    Route::post('/preview', [DashboardController::class, 'previewNote'])->name('preview-note');
    Route::delete('/{note}', [DashboardController::class, 'deleteNote'])->name('delete-note');
    Route::put('/{note}', [DashboardController::class, 'updateNote'])->name('update-note');
});
