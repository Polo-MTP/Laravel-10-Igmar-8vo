<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\MfaSetupController;

Route::redirect('/', '/login');

Route::get('/mfa/setup/{email}', [MfaSetupController::class, 'showSetupForm'])
    ->name('mfa.setup')
    ->middleware('signed');

Route::prefix('api')->group(function () {
    require __DIR__.'/auth.php';
});


// Rutas accesibles solo para no autenticados (invitados)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');

    Route::get('/register', function () {
        return view('register');
    })->name('register');
});

// Rutas protegidas (Reclaman estar autenticado y activo en el servidor)
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/perfil', function () {
        return view('perfil');
    })->name('perfil');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('role:Usuario,Administrador')->name('dashboard');

    Route::get('/dashboard-admin', function () {
        return view('dashboard-admin');
    })->middleware('role:Administrador')->name('dashboard-admin');

    Route::get('/dashboard-invitado', function () {
        return view('dashboard-invitado');
    })->middleware('role:Invitado')->name('dashboard-invitado');

    Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
});
