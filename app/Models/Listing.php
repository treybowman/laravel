<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Listing extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'venue_id',
        'title',
        'quantity',
        'section',
        'row',
        'seat_numbers',
        'asking_price',
        'willing_to_trade',
        'trade_notes',
        'transfer_method',
        'payment_methods',
        'notes',
        'status',
        'is_featured',
        'featured_until',
        'expires_at',
        'views_count',
        'social_push',
        'affiliate_url',
        'affiliate_label',
        'rejection_reason',
    ];

    protected $casts = [
        'payment_methods' => 'array',
        'willing_to_trade' => 'boolean',
        'is_featured' => 'boolean',
        'social_push' => 'boolean',
        'featured_until' => 'datetime',
        'expires_at' => 'datetime',
        'asking_price' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function messages(): HasManyThrough
    {
        return $this->hasManyThrough(Message::class, Conversation::class);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function feedbackRequests(): HasMany
    {
        return $this->hasMany(FeedbackRequest::class);
    }

    public function featuredQueue(): HasMany
    {
        return $this->hasMany(FeaturedQueue::class);
    }

    public function affiliateClicks(): HasMany
    {
        return $this->hasMany(AffiliateClick::class);
    }

    public function socialPushes(): HasMany
    {
        return $this->hasMany(SocialPush::class);
    }
}
