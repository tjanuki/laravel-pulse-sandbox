<?php

use App\Http\Controllers\Api\ServerRegistrationController;
use App\Http\Controllers\Api\StatusMetricController;
use App\Http\Controllers\ApiTestController;
use App\Http\Middleware\VerifyServerApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Test route to verify API functionality
Route::get('/test', [ApiTestController::class, 'test']);

// Server Registration Routes (Public)
Route::post('/servers/register', [ServerRegistrationController::class, 'register'])
    ->name('api.servers.register');

// Server Management Routes (Protected by auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/servers', [ServerRegistrationController::class, 'index'])
        ->name('api.servers.index');
    
    Route::get('/servers/{id}', [ServerRegistrationController::class, 'show'])
        ->name('api.servers.show');
    
    Route::put('/servers/{id}/activate', [ServerRegistrationController::class, 'activate'])
        ->name('api.servers.activate');
    
    Route::put('/servers/{id}/deactivate', [ServerRegistrationController::class, 'deactivate'])
        ->name('api.servers.deactivate');
    
    // Status Metrics Routes for Viewing
    Route::get('/status-metrics', [StatusMetricController::class, 'getLatest'])
        ->name('api.status-metrics.latest');
});

// Authenticated Server Routes
Route::middleware([VerifyServerApiKey::class])->group(function () {
    // Status Metrics API Routes
    Route::post('/status-metrics', [StatusMetricController::class, 'store'])
        ->name('api.status-metrics.store');
});
