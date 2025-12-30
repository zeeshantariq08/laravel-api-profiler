<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('api_profiler_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('request_id');
            $table->string('url');
            $table->string('type');  // slow, db_spike, http_spike
            $table->float('value');
            $table->float('baseline');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('api_profiler_alerts');
    }
};
