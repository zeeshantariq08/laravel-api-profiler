<?php

namespace ZeeshanTariq\LaravelApiProfiler\Services;

use Illuminate\Support\Facades\DB;
use ZeeshanTariq\LaravelApiProfiler\Models\ApiProfilerBaseline;

class BaselineCalculator
{
    public static function rebuild(): void
    {
        // Use last 24 hours of data
        $rows = DB::table('api_profiler_logs')
            ->where('created_at', '>=', now()->subDay())
            ->get()
            ->groupBy('url');

        foreach ($rows as $url => $requests) {

            $durations = $requests->pluck('duration_ms')->sort()->values();
            $count = $durations->count();

            if ($count < 10) {
                continue; // not enough data yet
            }

            $avg = $durations->avg();
            $p95 = $durations[(int) ($count * 0.95)];

            ApiProfilerBaseline::updateOrCreate(
                ['url' => $url],
                [
                    'avg_ms' => round($avg, 2),
                    'p95_ms' => round($p95, 2),
                    'avg_db_ms' => round($requests->avg('db_time_ms'), 2),
                    'avg_http_ms' => round($requests->avg('http_time_ms'), 2),
                ]
            );
        }
    }
}
