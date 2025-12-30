<?php

namespace ZeeshanTariq\LaravelApiProfiler\Models;

use Illuminate\Database\Eloquent\Model;

class ApiProfilerAlert extends Model
{
    protected $fillable = [
        'request_id',
        'url',
        'type',
        'value',
        'baseline'
    ];
}
