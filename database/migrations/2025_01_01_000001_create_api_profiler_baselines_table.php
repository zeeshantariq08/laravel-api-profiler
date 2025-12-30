<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('api_profiler_baselines', function (Blueprint $table) {
            $table->id();
            $table->string('url')->unique();
            $table->float('avg_ms');
            $table->float('p95_ms');
            $table->float('avg_db_ms');
            $table->float('avg_http_ms');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_profiler_baselines');
    }
};
