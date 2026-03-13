<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailBlastOpen extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'blast_id',
        'user_id',
        'opened_at',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function blast(): BelongsTo
    {
        return $this->belongsTo(EmailBlast::class, 'blast_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
