<?php

namespace ZeeshanTariq\LaravelApiProfiler\Commands;

use Illuminate\Console\Command;
use ZeeshanTariq\LaravelApiProfiler\Services\BaselineCalculator;

class RebuildBaselines extends Command
{
    protected $signature = 'api-profiler:rebuild-baselines';
    protected $description = 'Recalculate API performance baselines';

    public function handle()
    {
        BaselineCalculator::rebuild();
        $this->info('API Profiler baselines rebuilt');
    }
}
