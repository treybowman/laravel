<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Cashier\Billable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, Billable, Impersonate;

    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'zip_code',
        'favorite_teams',
        'avatar_path',
        'bio',
        'subscription_tier',
        'is_verified_sth',
        'is_admin',
        'is_super_admin',
        'is_banned',
        'banned_reason',
        'ban_expires_at',
        'probation',
        'listings_approved_count',
        'trust_level',
        'bst_score_override',
        'credits_balance',
        'referral_code',
        'phone_number',
        'phone_verified',
        'sms_alerts_enabled',
        'last_active_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'favorite_teams' => 'array',
            'is_verified_sth' => 'boolean',
            'is_admin' => 'boolean',
            'is_super_admin' => 'boolean',
            'is_banned' => 'boolean',
            'ban_expires_at' => 'datetime',
            'probation' => 'boolean',
            'phone_verified' => 'boolean',
            'sms_alerts_enabled' => 'boolean',
            'last_active_at' => 'datetime',
            'bst_score_override' => 'decimal:2',
        ];
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function conversationsAsBuyer(): HasMany
    {
        return $this->hasMany(Conversation::class, 'buyer_id');
    }

    public function conversationsAsSeller(): HasMany
    {
        return $this->hasMany(Conversation::class, 'seller_id');
    }

    public function feedbacksGiven(): HasMany
    {
        return $this->hasMany(Feedback::class, 'reviewer_id');
    }

    public function feedbacksReceived(): HasMany
    {
        return $this->hasMany(Feedback::class, 'reviewee_id');
    }

    public function savedSearches(): HasMany
    {
        return $this->hasMany(SavedSearch::class);
    }

    public function creditTransactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function promoCodes(): HasMany
    {
        return $this->hasMany(PromoCode::class, 'created_by');
    }

    public function promoCodeRedemptions(): HasMany
    {
        return $this->hasMany(PromoCodeRedemption::class);
    }

    public function bstScore(): float
    {
        if ($this->bst_score_override !== null) {
            return (float) $this->bst_score_override;
        }

        $feedbacks = $this->feedbacksReceived()->where('is_visible', true)->get();
        $total = $feedbacks->count();

        if ($total === 0) {
            return 0.0;
        }

        $positives = $feedbacks->where('rating', 'positive')->count();

        return round(($positives / $total) * 100, 1);
    }

    public function bstTransactionCount(): int
    {
        return $this->feedbacksReceived()->where('is_visible', true)->count();
    }

    public function isOnProbation(): bool
    {
        return (bool) $this->probation;
    }

    public function canCreateListing(): bool
    {
        if ($this->subscription_tier !== 'free') {
            return true;
        }

        $maxFree = (int) setting('platform.max_free_listings', 1);
        $activeCount = $this->listings()->where('status', 'active')->count();

        return $activeCount < $maxFree;
    }

    public function canCreateSavedSearch(): bool
    {
        if ($this->subscription_tier === 'pro') {
            return true;
        }

        $limit = match ($this->subscription_tier) {
            'member' => (int) setting('platform.max_member_saved_searches', 5),
            default => (int) setting('platform.max_free_saved_searches', 1),
        };

        return $this->savedSearches()->where('is_active', true)->count() < $limit;
    }
}
