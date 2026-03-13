<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailBlast extends Model
{
    protected $fillable = [
        'subject',
        'body',
        'segment',
        'sent_at',
        'recipient_count',
        'open_count',
        'created_by',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function opens(): HasMany
    {
        return $this->hasMany(EmailBlastOpen::class, 'blast_id');
    }
}
