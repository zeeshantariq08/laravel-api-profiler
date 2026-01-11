<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_profiler_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_id')->unique();
            $table->string('method');
            $table->string('url');
            $table->integer('status_code')->nullable();
            $table->float('duration_ms')->nullable();
            $table->float('db_ms')->nullable();
            $table->float('http_ms')->nullable();
            $table->integer('queries')->nullable();
            $table->bigInteger('memory_peak')->nullable();
            $table->boolean('slow')->default(false);
            $table->string('bottleneck')->nullable();
            $table->json('timings')->nullable();     // middleware/controller/db/http/response
            $table->json('n_plus_one')->nullable();  // detected N+1 queries
            $table->json('timeline')->nullable();
            $table->json('http_calls')->nullable();
            $table->json('queries_list')->nullable(); // list of SQL queries executed

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_profiler_logs');
    }
};
