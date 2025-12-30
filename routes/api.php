<?php

use Illuminate\Support\Facades\Route;
use ZeeshanTariq\LaravelApiProfiler\Controllers\ProfilerController;

Route::prefix('api-profiler')->group(function () {
    Route::get('/requests', [ProfilerController::class, 'index']);
    Route::get('/requests/{requestId}', [ProfilerController::class, 'show']);
    Route::get('/routes', [ProfilerController::class, 'routes']);
    Route::get('/alerts', [ProfilerController::class, 'alerts']);
    Route::get('/routes/{url}/trend', [ProfilerController::class, 'trend']);



});
