<?php

use App\Services\SettingsService;

if (! function_exists('setting')) {
    /**
     * Resolve a dynamic site setting, falling back to the given default.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return app(SettingsService::class)->get($key, $default);
    }
}
