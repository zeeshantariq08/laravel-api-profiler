<?php

namespace ZeeshanTariq\LaravelApiProfiler\Models;

use Illuminate\Database\Eloquent\Model;

class ApiProfilerBaseline extends Model
{
    protected $table = 'api_profiler_baselines';

    protected $fillable = [
        'url',
        'avg_ms',
        'p95_ms',
        'avg_db_ms',
        'avg_http_ms',
    ];
}
