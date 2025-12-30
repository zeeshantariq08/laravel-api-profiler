<?php

namespace ZeeshanTariq\LaravelApiProfiler\Services;

use ZeeshanTariq\LaravelApiProfiler\Models\ApiProfilerBaseline;
use ZeeshanTariq\LaravelApiProfiler\Models\ApiProfilerAlert;
use ZeeshanTariq\LaravelApiProfiler\Models\ApiProfilerLog;

class AlertEngine
{
    public static function analyze(ApiProfilerLog $log)
    {
        $baseline = ApiProfilerBaseline::where('url', $log->url)->first();
        if (! $baseline) return;

        // Slow request
        if ($log->duration_ms > $baseline->p95_ms * 1.5) {
            self::create($log, 'slow', $log->duration_ms, $baseline->p95_ms);
        }

        // DB spike
        if ($log->db_time_ms > $baseline->avg_db_ms * 2) {
            self::create($log, 'db_spike', $log->db_time_ms, $baseline->avg_db_ms);
        }

        // HTTP spike
        if ($log->http_time_ms > $baseline->avg_http_ms * 2) {
            self::create($log, 'http_spike', $log->http_time_ms, $baseline->avg_http_ms);
        }
    }

    protected static function create($log, $type, $value, $baseline)
    {
        ApiProfilerAlert::create([
            'request_id' => $log->request_id,
            'url' => $log->url,
            'type' => $type,
            'value' => $value,
            'baseline' => $baseline,
        ]);
    }
}
