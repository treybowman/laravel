<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeaturedQueue extends Model
{
    protected $table = 'featured_queue';

    protected $fillable = [
        'listing_id',
        'event_id',
        'user_id',
        'duration_hours',
        'credits_spent',
        'status',
        'activates_at',
        'expires_at',
    ];

    protected $casts = [
        'activates_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
