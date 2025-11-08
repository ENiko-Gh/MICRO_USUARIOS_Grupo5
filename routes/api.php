<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// ==========================================================
// GRUPO PRINCIPAL V1: Prefijo /api/v1
// ==========================================================
Route::prefix('v1')->group(function () {
    
    // ------------------------------------
    // 1. RUTAS PÚBLICAS (NO requieren Token)
    // ------------------------------------
    
    // REGISTRO: Permite crear un usuario (POST /api/v1/users)
    Route::post('users', [UserController::class, 'store']); 
    
    // LOGIN: Permite obtener el token (POST /api/v1/login)
    Route::post('login', [AuthController::class, 'login']); 

    
    // ----------------------------------------------------
    // 2. RUTAS PROTEGIDAS (REQUIEREN Token 'auth:sanctum')
    // ----------------------------------------------------
    
    Route::middleware('auth:sanctum')->group(function () {
        
        // CRUD Restante: index, show, update, destroy
        // Excluye 'store' porque ya fue definida como ruta pública arriba.
        Route::apiResource('users', UserController::class)->except(['store']);
        
        // LOGOUT: Cierra la sesión (POST /api/v1/logout)
        Route::post('logout', [AuthController::class, 'logout']);
    });
});