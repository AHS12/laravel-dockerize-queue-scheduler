<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthController;
use App\Jobs\ExampleJob;

// Health check endpoints
Route::get('/health', [HealthController::class, 'check']);
Route::get('/health/simple', [HealthController::class, 'simple']);

// User endpoint
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Queue job endpoint for testing
Route::get('/queue-job', function () {
    ExampleJob::dispatch();
    return response()->json(['message' => 'Job queued successfully']);
});

Route::get('/sazid', function () {
    return response()->json(['message' => 'Sazid healed successfully']);
});
