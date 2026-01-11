<?php

namespace ZeeshanTariq\LaravelApiProfiler\Models;

use Illuminate\Database\Eloquent\Model;

class ApiProfilerLog extends Model
{
    protected $table = 'api_profiler_logs';

    protected $fillable = [
        'request_id',
        'method',
        'url',
        'status_code',
        'duration_ms',
        'db_ms',
        'http_ms',
        'queries',
        'memory_peak',
        'slow',
        'bottleneck',
        'timings',
        'n_plus_one',
        'timeline',
        'http_calls',
        'queries_list',
    ];

    protected $casts = [
        'timings' => 'array',
        'n_plus_one' => 'array',
        'slow' => 'boolean',
        'queries_list' => 'array',
        'http_calls' => 'array',
        'timeline' => 'array',
    ];

    public function getDbTimeMsAttribute()
    {
        return $this->timings['db'] ?? $this->db_ms ?? 0;
    }

    public function getHttpTimeMsAttribute()
    {
        return $this->timings['http'] ?? $this->http_ms ?? 0;
    }

    public function getMiddlewareTimeMsAttribute()
    {
        return $this->timings['middleware'] ?? 0;
    }

    public function getControllerTimeMsAttribute()
    {
        return $this->timings['controller'] ?? 0;
    }

    public function getQueriesListAttribute()
    {
        $value = $this->attributes['queries_list'] ?? null;
        if ($value && is_string($value)) {
            return json_decode($value, true) ?? [];
        }
        return $value ?? [];
    }

    public function getHttpCallsListAttribute()
    {
        return $this->http_calls ?? [];
    }

    public static function routeStats()
    {
        return self::selectRaw('
        url,
        COUNT(*) as calls,
        AVG(duration_ms) as avg_ms,
        SUM(slow = 1) / COUNT(*) * 100 as slow_pct,
        AVG(db_ms) as avg_db,
        AVG(http_ms) as avg_http
    ')
            ->groupBy('url')
            ->orderByDesc('avg_ms')
            ->get();
    }

}
