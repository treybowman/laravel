<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    private const CACHE_TTL = 3600;
    private const CACHE_KEY = 'app_settings';

    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();
        return $settings->get($key, $default);
    }

    public function set(string $key, mixed $value): void
    {
        Setting::where('key', $key)->update(['value' => $value]);
        $this->flush();
    }

    public function group(string $group): Collection
    {
        return Setting::where('group', $group)->get();
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function all(): Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Setting::all()->mapWithKeys(fn($s) => [$s->key => $s->castValue()]);
        });
    }
}
