<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServerController;
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('servers')->group(function () {
        Route::get('/slow', [ServerController::class, 'slowList']);
        Route::get('/optimized', [ServerController::class, 'optimizedList']);
        Route::get('/', [ServerController::class, 'index']);
        Route::post('/', [ServerController::class, 'store']);
        Route::get('/{server}', [ServerController::class, 'show']);
        Route::put('/{server}', [ServerController::class, 'update']);
        Route::delete('/{server}', [ServerController::class, 'destroy']);
        Route::post('/bulk', [ServerController::class, 'bulkAction']);
    });
});
