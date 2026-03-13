<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained('venues')->cascadeOnDelete();
            $table->string('title', 255);
            $table->tinyInteger('quantity')->unsigned();
            $table->string('section', 50)->nullable();
            $table->string('row', 20)->nullable();
            $table->string('seat_numbers', 100)->nullable();
            $table->decimal('asking_price', 8, 2);
            $table->boolean('willing_to_trade')->default(false);
            $table->text('trade_notes')->nullable();
            $table->enum('transfer_method', ['pdf_download', 'email_forward', 'mobile_transfer', 'will_call', 'in_person']);
            $table->json('payment_methods');
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'sold', 'expired', 'pending_approval'])->default('pending_approval');
            $table->boolean('is_featured')->default(false);
            $table->dateTime('featured_until')->nullable();
            $table->dateTime('expires_at');
            $table->integer('views_count')->default(0);
            $table->boolean('social_push')->default(false);
            $table->string('affiliate_url')->nullable();
            $table->string('affiliate_label', 100)->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
