<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $fillable = [
        'reviewer_id',
        'reviewee_id',
        'listing_id',
        'transaction_type',
        'rating',
        'comment',
        'is_visible',
        'admin_note',
        'feedback_window_closes_at',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'feedback_window_closes_at' => 'datetime',
    ];

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }
}
