<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialPush extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'listing_id',
        'platform',
        'message',
        'status',
        'pushed_at',
        'error_message',
        'created_at',
    ];

    protected $casts = [
        'pushed_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }
}
