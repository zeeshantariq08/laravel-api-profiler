<?php

namespace ZeeshanTariq\LaravelApiProfiler\Services;

use ZeeshanTariq\LaravelApiProfiler\Models\ApiProfilerBaseline;
use ZeeshanTariq\LaravelApiProfiler\Models\ApiProfilerAlert;
use ZeeshanTariq\LaravelApiProfiler\Models\ApiProfilerLog;

class AlertEngine
{
    public static function process(ApiProfilerLog $log): void
    {
        $baseline = ApiProfilerBaseline::firstOrCreate(
            ['url' => $log->url],
            [
                'avg_ms' => $log->duration_ms,
                'p95_ms' => $log->duration_ms,
                'avg_db_ms' => $log->db_ms,
                'avg_http_ms' => $log->http_ms,
            ]
        );

        $recentLogs = ApiProfilerLog::where('url', $log->url)
            ->latest()
            ->limit(config('api-profiler.baseline_sample_size'))
            ->get();

        if ($recentLogs->isNotEmpty()) {
            $baseline->avg_ms = $recentLogs->avg('duration_ms') ?? $log->duration_ms;
            $sortedDurations = $recentLogs->pluck('duration_ms')->filter()->sort()->values();
            $p95Index = intval(0.95 * $sortedDurations->count());
            $baseline->p95_ms = $sortedDurations->get($p95Index) ?? $log->duration_ms;
            $baseline->avg_db_ms = $recentLogs->avg('db_ms') ?? $log->db_ms ?? 0;
            $baseline->avg_http_ms = $recentLogs->avg('http_ms') ?? $log->http_ms ?? 0;
        } else {
            $baseline->avg_ms = $log->duration_ms;
            $baseline->p95_ms = $log->duration_ms;
            $baseline->avg_db_ms = $log->db_ms ?? 0;
            $baseline->avg_http_ms = $log->http_ms ?? 0;
        }
        $baseline->save();

        if ($log->duration_ms > $baseline->avg_ms * 1.5) {
            ApiProfilerAlert::create([
                'request_id' => $log->request_id,
                'url' => $log->url,
                'type' => 'slow_request',
                'value' => $log->duration_ms,
                'baseline' => $baseline->avg_ms,
            ]);
        }

        if ($log->memory_peak > config('api-profiler.high_memory_bytes')) {
            ApiProfilerAlert::create([
                'request_id' => $log->request_id,
                'url' => $log->url,
                'type' => 'high_memory',
                'value' => $log->memory_peak,
                'baseline' => config('api-profiler.high_memory_bytes'),
            ]);
        }

        if (!empty($log->n_plus_one)) {
            ApiProfilerAlert::create([
                'request_id' => $log->request_id,
                'url' => $log->url,
                'type' => 'n_plus_one',
                'value' => count($log->n_plus_one),
                'baseline' => config('api-profiler.n_plus_one_threshold'),
            ]);
        }
    }
}
