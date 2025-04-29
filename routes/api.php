<?php

use App\Http\Controllers\Api\StatusMetricController;
use App\Http\Controllers\ApiTestController;
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

// Status Metrics API Routes
Route::post('/status-metrics', [StatusMetricController::class, 'store'])
    ->name('api.status-metrics.store');

Route::get('/status-metrics', [StatusMetricController::class, 'getLatest'])
    ->name('api.status-metrics.latest');
