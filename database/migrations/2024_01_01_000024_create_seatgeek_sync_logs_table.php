<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seatgeek_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('run_at');
            $table->integer('events_synced');
            $table->integer('events_expired');
            $table->enum('status', ['success', 'failed']);
            $table->text('error_message')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->dateTime('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seatgeek_sync_logs');
    }
};
