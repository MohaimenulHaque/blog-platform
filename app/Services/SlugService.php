<?php

namespace App\Services;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SlugService
{
    /**
     * Generate a unique slug for the given title, appending a numeric suffix
     * when the base slug is already in use (including soft-deleted records).
     *
     * @param  class-string  $model
     */
    public function unique(string $title, string $model, ?int $ignoreId = null, ?string $slug = null): string
    {
        $base = Str::slug($slug ?: $title);

        if (blank($base)) {
            $base = 'item';
        }

        $query = in_array(SoftDeletes::class, class_uses_recursive($model), true)
            ? $model::withTrashed()
            : $model::query();

        if ($ignoreId) {
            $query->whereKeyNot($ignoreId);
        }

        $candidate = $base;
        $suffix = 2;

        while ($query->clone()->where('slug', $candidate)->exists()) {
            $candidate = $base.'-'.$suffix;
            $suffix++;
        }

        return $candidate;
    }
}
