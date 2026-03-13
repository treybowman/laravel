<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->string('zip_code', 10)->nullable()->after('username');
            $table->json('favorite_teams')->nullable()->after('zip_code');
            $table->string('avatar_path')->nullable()->after('favorite_teams');
            $table->text('bio')->nullable()->after('avatar_path');
            $table->enum('subscription_tier', ['free', 'member', 'pro'])->default('free')->after('bio');
            $table->boolean('is_verified_sth')->default(false)->after('subscription_tier');
            $table->boolean('is_admin')->default(false)->after('is_verified_sth');
            $table->boolean('is_super_admin')->default(false)->after('is_admin');
            $table->boolean('is_banned')->default(false)->after('is_super_admin');
            $table->text('banned_reason')->nullable()->after('is_banned');
            $table->dateTime('ban_expires_at')->nullable()->after('banned_reason');
            $table->boolean('probation')->default(true)->after('ban_expires_at');
            $table->integer('listings_approved_count')->default(0)->after('probation');
            $table->enum('trust_level', ['none', 'trusted', 'power_seller'])->default('none')->after('listings_approved_count');
            $table->decimal('bst_score_override', 5, 2)->nullable()->after('trust_level');
            $table->integer('credits_balance')->default(0)->after('bst_score_override');
            $table->string('referral_code', 20)->unique()->nullable()->after('credits_balance');
            $table->string('phone_number', 20)->nullable()->after('referral_code');
            $table->boolean('phone_verified')->default(false)->after('phone_number');
            $table->boolean('sms_alerts_enabled')->default(false)->after('phone_verified');
            $table->dateTime('last_active_at')->nullable()->after('sms_alerts_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username', 'zip_code', 'favorite_teams', 'avatar_path', 'bio',
                'subscription_tier', 'is_verified_sth', 'is_admin', 'is_super_admin',
                'is_banned', 'banned_reason', 'ban_expires_at', 'probation',
                'listings_approved_count', 'trust_level', 'bst_score_override',
                'credits_balance', 'referral_code', 'phone_number', 'phone_verified',
                'sms_alerts_enabled', 'last_active_at',
            ]);
        });
    }
};
