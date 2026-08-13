<?php

namespace App\Models;

use App\Enums\CategoryStatus;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'status',
        'meta_title',
        'meta_description',
    ];

    /**
     * The posts that belong to the category.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * The published posts in the category.
     */
    public function publishedPosts(): HasMany
    {
        return $this->posts()->published()->public();
    }

    /**
     * Scope to only active categories.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CategoryStatus::Active);
    }

    /**
     * Scope to search categories by name or description.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = mb_strtolower(trim($term));

        return $query->where(function (Builder $q) use ($term): void {
            $q->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(description) LIKE ?', ["%{$term}%"]);
        });
    }

    /**
     * Determine whether the category is active.
     */
    public function isActive(): bool
    {
        return $this->status === CategoryStatus::Active->value;
    }

    /**
     * The human friendly status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return CategoryStatus::from($this->status)->label();
    }

    /**
     * The badge variant for the current status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return CategoryStatus::from($this->status)->badge();
    }

    /**
     * The public URL for the category image.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        return asset('storage/'.$this->image);
    }

    /**
     * The URL to view the category on the public site.
     */
    public function getUrlAttribute(): string
    {
        return route('categories.show', $this->slug);
    }
}
