<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ContentCache
{
    /**
     * The public-facing cache keys that must be refreshed whenever content changes.
     *
     * @var list<string>
     */
    public const KEYS = [
        'home.trending',
        'home.categories',
        'home.authors',
        'home.stats',
        'blog.sidebar.categories',
        'blog.sidebar.tags',
        'blog.sidebar.authors',
        'search.popular.categories',
        'search.popular.tags',
        'sitemap.xml',
    ];

    /**
     * Forget every cached public page fragment so it is rebuilt lazily.
     */
    public static function flush(): void
    {
        foreach (self::KEYS as $key) {
            Cache::forget($key);
        }
    }
}
