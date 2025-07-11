<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Jobs\ExampleJob;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/queue-job', function () {
    ExampleJob::dispatch();
    return response()->json(['message' => 'Job queued successfully']);
});

Route::get('/test', function () {
    return response()->json(['message' => 'API is working!', 'timestamp' => now()]);
});
