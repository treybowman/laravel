<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->foreignId('venue_id')->nullable()->constrained('venues')->nullOnDelete();
            $table->foreignId('event_id')->nullable()->constrained('events')->nullOnDelete();
            $table->string('team')->nullable();
            $table->decimal('max_price', 8, 2)->nullable();
            $table->integer('min_quantity')->nullable();
            $table->enum('transfer_method', ['pdf_download', 'email_forward', 'mobile_transfer', 'will_call', 'in_person'])->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('last_alerted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_searches');
    }
};
