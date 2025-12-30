<?php

namespace ZeeshanTariq\LaravelApiProfiler;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Http\Client\Events\ResponseReceived;
use ZeeshanTariq\LaravelApiProfiler\Middleware\ApiProfilerMiddleware;
use ZeeshanTariq\LaravelApiProfiler\Profiler\Profiler;

class LaravelApiProfilerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register middleware
        App::make('router')->aliasMiddleware('api-profiler', ApiProfilerMiddleware::class);
        logger()->info('API PROFILER MIDDLEWARE REGISTERED');

        /*
        |--------------------------------------------------------------------------
        | SQL Query Timing
        |--------------------------------------------------------------------------
        */
        DB::listen(fn($query) => Profiler::addQuery($query->sql, $query->time));


        /*
        |--------------------------------------------------------------------------
        | HTTP Client Timing
        |--------------------------------------------------------------------------
        */
        Event::listen(RequestSending::class, function ($event) {
            $event->request->__start = microtime(true);
        });

        Event::listen(ResponseReceived::class, function ($event) {
            $start = $event->request->__start ?? null;

            if ($start) {
                Profiler::addHttp(
                    (microtime(true) - $start) * 1000
                );
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Memory Peak
        |--------------------------------------------------------------------------
        */
        app()->terminating(function () {
            Profiler::addMemory(memory_get_peak_usage(true));
        });

        /*
        |--------------------------------------------------------------------------
        | Package Assets
        |--------------------------------------------------------------------------
        */
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-api-profiler');

    }

    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \ZeeshanTariq\LaravelApiProfiler\Commands\RebuildBaselines::class,
            ]);
        }
    }

}
