<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackRequest extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'listing_id',
        'seller_id',
        'buyer_id',
        'seller_feedback_given',
        'buyer_feedback_given',
        'token',
        'expires_at',
        'created_at',
    ];

    protected $casts = [
        'seller_feedback_given' => 'boolean',
        'buyer_feedback_given' => 'boolean',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
