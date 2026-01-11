<?php

namespace ZeeshanTariq\LaravelApiProfiler\Profiler;

class Profiler
{
    protected static ?RequestProfile $current = null;

    public static function start(string $route, string $method): void
    {
        static::$current = new RequestProfile();
        static::$current->route = $route;
        static::$current->method = $method;
        static::$current->start = microtime(true);
    }

    public static function end(): ?RequestProfile
    {
        if (! static::$current) return null;

        static::$current->end = microtime(true);

        $total = static::$current->duration();

        // Slow request
        if ($total > 500) {
            static::$current->slow = true;
        }

        // Root cause
        if (static::$current->dbTime > 200) {
            static::$current->bottleneck = 'database';
        }
        elseif (static::$current->httpTime > 300) {
            static::$current->bottleneck = 'external_api';
        }
        elseif (static::$current->memory > 128 * 1024 * 1024) {
            static::$current->bottleneck = 'memory';
        }
        else {
            static::$current->bottleneck = 'application';
        }
        // N+1 detection
        $counts = array_count_values(static::$current->queriesList);
        foreach ($counts as $query => $count) {
            if ($count > 5) { // Threshold
                static::$current->nPlusOne[] = [
                    'query' => $query,
                    'count' => $count,
                    'suggestion' => 'Possible N+1 detected, consider eager loading with with()',
                ];
            }
        }

        return static::$current;
    }


    public static function addQuery(string $sql, float $time): void
    {
        if (! static::$current) return;

        static::$current->dbTime += $time;
        static::$current->queries++;

        // Normalize SQL for N+1 detection
        $normalized = preg_replace('/\s+/', ' ', trim($sql));
        static::$current->queriesList[] = $normalized;
    }

    public static function addMemory(int $bytes): void
    {
        if (! static::$current) return;

        static::$current->memory = $bytes;
    }

    public static function addHttp(string $url, float $time): void
    {
        if (!static::$current) return;

        static::$current->httpTime += $time;
        static::$current->httpCalls++;
        static::$current->httpCallsList[] = [
            'url' => $url,
            'duration_ms' => $time,
        ];
    }

}
