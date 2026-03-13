<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_blasts', function (Blueprint $table) {
            $table->id();
            $table->string('subject', 255);
            $table->longText('body');
            $table->string('segment', 100);
            $table->dateTime('sent_at')->nullable();
            $table->integer('recipient_count')->nullable();
            $table->integer('open_count')->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_blasts');
    }
};
