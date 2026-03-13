<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Crypt;

class EncryptedSetting implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): mixed
    {
        if (empty($value)) {
            return $value;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function set($model, string $key, $value, array $attributes): mixed
    {
        if (empty($value)) {
            return $value;
        }
        return Crypt::encryptString($value);
    }
}
