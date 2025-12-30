<?php

namespace ZeeshanTariq\LaravelApiProfiler\Profiler;

class RequestProfile
{
    public string $route;
    public string $method;
    public float $start;
    public float $end;

    public float $dbTime = 0;
    public float $httpTime = 0;
    public int $queries = 0;
    public int $httpCalls = 0;
    public int $memory = 0;
    public bool $slow = false;
    public ?string $bottleneck = null;
    public array $queriesList = [];
    public array $nPlusOne = [];
    public array $timeline = [];

    public function duration(): float
    {
        return ($this->end - $this->start) * 1000;
    }
}

