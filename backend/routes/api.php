<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BuildingController;
use App\Http\Controllers\Api\ApartmentController;


Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/me', [AuthController::class, 'me']);

    });

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/buildings/{building}/apartments', [ApartmentController::class, 'index']);
    Route::post('/buildings/{building}/apartments', [ApartmentController::class, 'store']);
    Route::get('/apartments/{apartment}', [ApartmentController::class, 'show']);
    Route::put('/apartments/{apartment}', [ApartmentController::class, 'update']);
    Route::delete('/apartments/{apartment}', [ApartmentController::class, 'destroy']);
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
