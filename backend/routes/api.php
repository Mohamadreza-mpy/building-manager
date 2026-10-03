<?php

use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\ApartmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BuildingController;
use App\Http\Controllers\Api\ChargeController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OwnerController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ResidentController;
use App\Http\Controllers\Api\ResidentRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', DashboardController::class);
    Route::apiResource('buildings', BuildingController::class);
    Route::get('/buildings/{building}/apartments', [ApartmentController::class, 'index']);
    Route::post('/buildings/{building}/apartments', [ApartmentController::class, 'store']);
    Route::apiResource('apartments', ApartmentController::class)->except(['index', 'store']);
    Route::get('/charges', [ChargeController::class, 'myCharges']);
    Route::get('/buildings/{building}/charges', [ChargeController::class, 'index']);
    Route::post('/buildings/{building}/charges', [ChargeController::class, 'store']);
    Route::get('/charges/{charge}', [ChargeController::class, 'show']);
    Route::put('/charges/{charge}', [ChargeController::class, 'update']);
    Route::post('/charges/{charge}/receipt', [ChargeController::class, 'submitReceipt']);
    Route::post('/charges/{charge}/approve-receipt', [ChargeController::class, 'approveReceipt']);
    Route::get('/buildings/{building}/expenses', [ExpenseController::class, 'index']);
    Route::post('/buildings/{building}/expenses', [ExpenseController::class, 'store']);
    Route::get('/buildings/{building}/announcements', [AnnouncementController::class, 'index']);
    Route::post('/buildings/{building}/announcements', [AnnouncementController::class, 'store']);
    Route::get('/requests', [ResidentRequestController::class, 'index']);
    Route::post('/requests', [ResidentRequestController::class, 'store']);
    Route::put('/requests/{residentRequest}', [ResidentRequestController::class, 'update']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{notification}/read', [NotificationController::class, 'read']);
    Route::post('/device-tokens', [DeviceTokenController::class, 'store']);
    Route::delete('/device-tokens', [DeviceTokenController::class, 'destroy']);
    Route::get('/owners', [OwnerController::class, 'index']);
    Route::post('/owners', [OwnerController::class, 'store']);
    Route::get('/residents', [ResidentController::class, 'index']);
    Route::post('/residents', [ResidentController::class, 'store']);
    Route::get('/buildings/{building}/reports/summary', [ReportController::class, 'summary']);
});
