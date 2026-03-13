<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedSearch extends Model
{
    protected $fillable = [
        'user_id',
        'label',
        'venue_id',
        'event_id',
        'team',
        'max_price',
        'min_quantity',
        'transfer_method',
        'is_active',
        'last_alerted_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_alerted_at' => 'datetime',
        'max_price' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
