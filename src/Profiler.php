<?php

namespace ZeeshanTariq\LaravelApiProfiler;

use Illuminate\Support\Str;
use ZeeshanTariq\LaravelApiProfiler\Models\ApiProfilerLog;
use ZeeshanTariq\LaravelApiProfiler\Services\AlertEngine;

class Profiler
{
    protected static array $queries = [];
    protected static array $http = [];
    protected static float $memory = 0;

    protected static float $startTime = 0;
    protected static float $endTime = 0;
    protected static string $requestId;


    public static function start(): void
    {
        self::$requestId = (string) Str::uuid();
        self::$startTime = microtime(true);
        self::$queries = [];
        self::$http = [];
    }


    public static function stop($response): void
    {
        self::$endTime = microtime(true);

        $log = ApiProfilerLog::create([
            'request_id' => self::$requestId,
            'method' => request()->method(),
            'url' => request()->path(),
            'status_code' => $response->getStatusCode(),
            'duration_ms' => round((self::$endTime - self::$startTime) * 1000, 2),
            'memory_peak' => memory_get_peak_usage(true),
            'queries' => self::$queries,
            'http_calls' => self::$http,
            'created_at' => now(),
        ]);
        AlertEngine::analyze($log);
    }


    public static function addQuery(string $sql, float $time): void
    {
        self::$queries[] = [
            'sql' => $sql,
            'time_ms' => $time,
        ];
    }

    public static function addHttp(string $url, float $time): void
    {
        self::$http[] = [
            'url' => $url,
            'time_ms' => round($time, 2),
        ];
    }

    public static function addMemory(int $bytes): void
    {
        self::$memory = $bytes;
    }
    public static function requestId(): string
    {
        return self::$requestId;
    }

}
