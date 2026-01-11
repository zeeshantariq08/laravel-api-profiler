<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable / Disable Profiler
    |--------------------------------------------------------------------------
    */

    'enabled' => env('API_PROFILER_ENABLED', true),

    'slow_request_threshold_ms' => 500, // alert threshold
    'high_memory_bytes' => 128 * 1024 * 1024,
    'n_plus_one_threshold' => 5,

    'baseline_sample_size' => 50, // how many samples to compute baseline

    /*
    |--------------------------------------------------------------------------
    | Dashboard Middleware
    |--------------------------------------------------------------------------
    */
    'middleware' => ['web', 'auth'],


];
