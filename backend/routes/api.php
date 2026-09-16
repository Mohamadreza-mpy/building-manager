<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BuildingController;


Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/me', [AuthController::class, 'me']);

    });

});

Route::middleware('auth:sanctum')
    ->prefix('buildings')
    ->group(function () {

        Route::get('/', [BuildingController::class, 'index']);

        Route::post('/', [BuildingController::class, 'store']);

        Route::get('/{building}', [BuildingController::class, 'show']);

        Route::put('/{building}', [BuildingController::class, 'update']);

        Route::delete('/{building}', [BuildingController::class, 'destroy']);

    });
