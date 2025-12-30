<?php

use ZeeshanTariq\LaravelApiProfiler\Controllers\DashboardController;


Route::prefix('api-profiler')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard']);
    Route::get('/requests', [DashboardController::class, 'requests']);
    Route::get('/requests/{requestId}', [DashboardController::class, 'show']);
    Route::get('/routes', [DashboardController::class, 'routes']);
    Route::get('/alerts', [DashboardController::class, 'alerts']);


});
