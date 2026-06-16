<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('role');
});


Route::middleware(['auth:sanctum', 'active'])->group(function () {

    // Rutas para Administradores
    Route::get('/admin-data', function () {
        return response()->json(['message' => '¡Hola Administrador! Aquí están los datos secretos.']);
    })->middleware('role:Administrador');

    Route::get('/admin/historical-data', [\App\Http\Controllers\Auth\AuditController::class, 'getHistoricalData'])
        ->middleware('role:Administrador');
        


    // Rutas para Usuarios y Administradores
    Route::get('/user-data', function () {
        return response()->json(['message' => '¡Hola! Estos son los datos de un usuario normal.']);
    })->middleware('role:Usuario,Administrador');

});
