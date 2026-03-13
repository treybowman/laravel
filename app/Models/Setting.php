<?php

namespace App\Models;

use App\Casts\EncryptedSetting;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    private static array $encryptedKeys = [
        'stripe.secret_key',
        'stripe.webhook_secret',
        'seatgeek.client_secret',
        'sms.twilio_auth_token',
        'mail.smtp_password',
        'social.facebook_page_access_token',
        'social.twitter_api_secret',
        'social.twitter_access_token_secret',
    ];

    public function getCasts(): array
    {
        $casts = parent::getCasts();

        if (isset($this->attributes['key']) && in_array($this->attributes['key'], self::$encryptedKeys)) {
            $casts['value'] = EncryptedSetting::class;
        }

        return $casts;
    }

    public function castValue(): mixed
    {
        $value = $this->value;

        return match ($this->type) {
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'array', 'json' => json_decode($value, true),
            default => $value,
        };
    }
}
