<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\MfaController;


// Usamos el nombre 'register' que definimos en el RouteServiceProvider
Route::middleware('throttle:register')->group(function () {
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::middleware('throttle:login')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);

    Route::post('/mfa/verify', [MfaController::class, 'verify']); 
    Route::post('/mfa/email/verify', [MfaController::class, 'verifyEmailOtp']);
    Route::post('/mfa/setup/confirm', [\App\Http\Controllers\Auth\MfaSetupController::class, 'confirmSetup']);
});

