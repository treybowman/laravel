<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();
            $table->enum('transaction_type', ['buy', 'sell', 'trade']);
            $table->enum('rating', ['positive', 'neutral', 'negative']);
            $table->text('comment')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->text('admin_note')->nullable();
            $table->dateTime('feedback_window_closes_at');
            $table->timestamps();

            $table->unique(['listing_id', 'reviewer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
