<?php

namespace App\Http\Controllers;

use Closure;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Remember a value in the cache, bypassing the cache while running tests
     * so that freshly seeded fixtures are always reflected.
     */
    protected function remember(string $key, Closure $callback, int $ttl = 600): mixed
    {
        if (app()->runningUnitTests()) {
            return $callback();
        }

        return Cache::remember($key, now()->addSeconds($ttl), $callback);
    }
}
