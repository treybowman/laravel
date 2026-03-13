<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeatgeekSyncLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'run_at',
        'events_synced',
        'events_expired',
        'status',
        'error_message',
        'duration_ms',
        'created_at',
    ];

    protected $casts = [
        'run_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}
