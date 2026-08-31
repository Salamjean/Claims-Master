<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PublicAlerteApiController;
use App\Http\Controllers\Api\Auth\AuthApiController;
use App\Http\Controllers\Api\Assure\AssureDashboardApiController;
use App\Http\Controllers\Api\Assure\AssuranceApiController;
use App\Http\Controllers\Api\Assure\SinistreApiController;

/*
|--------------------------------------------------------------------------
| API Routes - Claims Master
|--------------------------------------------------------------------------
*/

// --- 1. API PUBLIQUE ---
Route::prefix('v1/public')->group(function () {
    Route::post('/signaler-urgence', [PublicAlerteApiController::class, 'signalerUrgence']);
    Route::get('/suivi-alerte/{token}', [PublicAlerteApiController::class, 'suiviAlerte']);
});

// Fallback sans v1 pour compatibilité
Route::prefix('public')->group(function () {
    Route::post('/signaler-urgence', [PublicAlerteApiController::class, 'signalerUrgence']);
    Route::get('/suivi-alerte/{token}', [PublicAlerteApiController::class, 'suiviAlerte']);
});

// --- 2. API AUTHENTIFICATION (Assuré, Agent, Groupe) ---
Route::prefix('v1/auth')->group(function () {
    // Public auth routes
    Route::post('/login', [AuthApiController::class, 'login']);
    Route::post('/register', [AuthApiController::class, 'register']);

    // Protected auth routes (requiert le header: Authorization: Bearer <token>)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthApiController::class, 'me']);
        Route::post('/logout', [AuthApiController::class, 'logout']);
    });
});

// --- 3. API ESPACE ASSURÉ ---
Route::prefix('v1/assure')->middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [AssureDashboardApiController::class, 'index']);

    // Gestion des assurances / contrats
    Route::get('/assurances', [AssuranceApiController::class, 'index']);
    Route::post('/assurances/scan-attestation-ai', [AssuranceApiController::class, 'scanAttestationAI']);
    Route::get('/assurances/{id}', [AssuranceApiController::class, 'show']);
    Route::post('/assurances', [AssuranceApiController::class, 'store']);
    Route::match(['put', 'post'], '/assurances/{id}', [AssuranceApiController::class, 'update']);
    Route::delete('/assurances/{id}', [AssuranceApiController::class, 'destroy']);

    // Déclaration et suivi des sinistres
    Route::get('/sinistres', [SinistreApiController::class, 'index']);
    Route::get('/sinistres/{id}', [SinistreApiController::class, 'show']);
    Route::post('/sinistres', [SinistreApiController::class, 'store']);
    Route::delete('/sinistres/{id}', [SinistreApiController::class, 'destroy']);
    Route::get('/sinistres/{id}/tracking', [SinistreApiController::class, 'tracking']);
    Route::post('/sinistres/documents/{documentAttenduId}/upload', [SinistreApiController::class, 'uploadDocument']);
});
