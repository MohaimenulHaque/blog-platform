<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    /**
     * The cache key used to persist the settings map.
     */
    protected string $cacheKey = 'settings.all';

    /**
     * The cached settings loaded from the database.
     *
     * @var array<string, string|null>|null
     */
    protected ?array $cached = null;

    /**
     * Get a setting value, falling back to the provided default.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $value = Arr::get($this->all(), $key);

        return $value !== null && $value !== '' ? $value : $default;
    }

    /**
     * Get every persisted setting as a key/value map.
     *
     * @return array<string, string|null>
     */
    public function all(): array
    {
        if ($this->cached !== null) {
            return $this->cached;
        }

        return $this->cached = Cache::rememberForever(
            $this->cacheKey,
            fn (): array => Setting::query()->pluck('value', 'key')->all(),
        );
    }

    /**
     * Persist a batch of settings.
     *
     * @param  array<string, mixed>  $values
     */
    public function set(array $values, string $group = 'general'): void
    {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value === '' ? null : $value, 'group' => $group]
            );
        }

        $this->cached = null;
        $this->flush();
    }

    /**
     * Forget the cached settings so the next read hits the database.
     */
    public function flush(): void
    {
        $this->cached = null;

        Cache::forget($this->cacheKey);
    }
}
