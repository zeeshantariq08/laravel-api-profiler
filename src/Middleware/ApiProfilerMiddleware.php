<?php

namespace ZeeshanTariq\LaravelApiProfiler\Middleware;

use Closure;
use ZeeshanTariq\LaravelApiProfiler\Profiler\Profiler;
use ZeeshanTariq\LaravelApiProfiler\Models\ApiProfilerLog;
use Illuminate\Support\Str;

class ApiProfilerMiddleware
{
    public function handle($request, Closure $next)
    {
        $startMiddleware = microtime(true);

        // Start profiler
        Profiler::start($request->path(), $request->method());

        // Proceed with request
        $response = $next($request);

        $endMiddleware = microtime(true);

        // End profiler
        $profile = Profiler::end();

        // Compute waterfall timings
        $profile->timings = [
            'middleware' => ($endMiddleware - $startMiddleware) * 1000,
            'controller' => $response->headers->get('X-Controller-Time') ?? 0,
            'db' => $profile->dbTime,
            'http' => $profile->httpTime,
            'response' => $profile->duration() - $profile->dbTime - $profile->httpTime,
        ];

        // Save to DB
        ApiProfilerLog::create([
            'request_id' => (string) Str::uuid(),
            'method' => $profile->method,
            'url' => $profile->route,
            'status_code' => $response->getStatusCode(),
            'duration_ms' => $profile->duration(),
            'db_ms' => $profile->dbTime,
            'http_ms' => $profile->httpTime,
            'queries' => $profile->queries,
            'memory_peak' => $profile->memory,
            'slow' => $profile->slow,
            'bottleneck' => $profile->bottleneck,
            'timings' => $profile->timings,
            'timeline' => json_encode($profile->timeline),
            'n_plus_one' => $profile->nPlusOne,
        ]);

        return $response;
    }
}
