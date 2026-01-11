<?php

namespace ZeeshanTariq\LaravelApiProfiler\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiProfilerAlert extends Model
{
    protected $table = 'api_profiler_alerts';

    protected $fillable = [
        'request_id',
        'url',
        'type',
        'value',
        'baseline'
    ];

    protected $casts = [
        'value' => 'float',
        'baseline' => 'float',
    ];

    /**
     * Get the log associated with this alert
     */
    public function log(): BelongsTo
    {
        return $this->belongsTo(ApiProfilerLog::class, 'request_id', 'request_id');
    }
}
