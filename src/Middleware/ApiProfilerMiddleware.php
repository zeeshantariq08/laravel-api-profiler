<?php

namespace ZeeshanTariq\LaravelApiProfiler\Middleware;

use Closure;
use Illuminate\Support\Str;
use ZeeshanTariq\LaravelApiProfiler\Profiler\Profiler;
use ZeeshanTariq\LaravelApiProfiler\Models\ApiProfilerLog;
use ZeeshanTariq\LaravelApiProfiler\Services\AlertEngine;

class ApiProfilerMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!config('api-profiler.enabled')) {
            return $next($request);
        }

        $startMiddleware = microtime(true);

        Profiler::start($request->path(), $request->method());

        $response = $next($request);

        $endMiddleware = microtime(true);

        $profile = Profiler::end();

        if (!$profile) {
            return $response;
        }

        $timeline = [];

        $timeline[] = [
            'type' => 'middleware',
            'name' => 'Middleware',
            'duration' => ($endMiddleware - $startMiddleware) * 1000,
        ];

        $timeline[] = [
            'type' => 'controller',
            'name' => 'Controller',
            'duration' => $response->headers->get('X-Controller-Time') ?? 0,
        ];

        $dbTimeline = [];
        foreach ($profile->queriesList as $sql) {
            $dbTimeline[] = [
                'type' => 'db',
                'name' => $sql,
                'duration' => $profile->dbTime / max(1, count($profile->queriesList)), // approximate per-query
            ];
        }
        $timeline = array_merge($timeline, $dbTimeline);

        $httpTimeline = [];
        if (!empty($profile->httpCallsList)) {
            foreach ($profile->httpCallsList as $http) {
                $httpTimeline[] = [
                    'type' => 'http',
                    'name' => $http['url'],
                    'duration' => $http['duration_ms'],
                ];
            }
        }
        $timeline = array_merge($timeline, $httpTimeline);

        $timeline[] = [
            'type' => 'response',
            'name' => 'Response',
            'duration' => $profile->duration() - $profile->dbTime - $profile->httpTime,
        ];

        $log = ApiProfilerLog::create([
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
            'timings' => [
                'middleware' => ($endMiddleware - $startMiddleware) * 1000,
                'controller' => $response->headers->get('X-Controller-Time') ?? 0,
                'db' => $profile->dbTime,
                'http' => $profile->httpTime,
                'response' => $profile->duration() - $profile->dbTime - $profile->httpTime,
            ],
            'timeline' => json_encode($timeline),
            'n_plus_one' => $profile->nPlusOne,
            'http_calls' => $profile->httpCallsList ?? [],
            'queries_list' => $profile->queriesList ?? [],
        ]);
        AlertEngine::process($log);

        return $response;
    }
}
