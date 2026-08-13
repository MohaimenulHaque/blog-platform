<?php

namespace App\Models;

use App\Enums\PostStatus;
use App\Enums\PostVisibility;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'thumbnail',
        'author_id',
        'category_id',
        'status',
        'visibility',
        'published_at',
        'scheduled_at',
        'reading_time',
        'view_count',
        'like_count',
        'comment_count',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'scheduled_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Post $post): void {
            $post->uuid = $post->uuid ?: (string) Str::uuid();
        });
    }

    /**
     * The author that owns the post.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * The category the post belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * The tags attached to the post.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * All comments left on the post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The approved comments left on the post.
     */
    public function approvedComments(): HasMany
    {
        return $this->comments()->approved();
    }

    /**
     * The likes the post has received.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    /**
     * The users who liked the post.
     */
    public function likers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_likes')->withTimestamps();
    }

    /**
     * The bookmarks the post has received.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * The users who bookmarked the post.
     */
    public function bookmarkers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bookmarks')->withTimestamps();
    }

    /**
     * Determine whether the given user has liked the post.
     */
    public function isLikedBy(?User $user): bool
    {
        return $user !== null && $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the given user has bookmarked the post.
     */
    public function isBookmarkedBy(?User $user): bool
    {
        return $user !== null && $this->bookmarks()->where('user_id', $user->id)->exists();
    }

    /**
     * Recompute and store the approved comment count.
     */
    public function syncCommentCount(): int
    {
        $count = $this->comments()->approved()->count();

        $this->forceFill(['comment_count' => $count])->saveQuietly();

        return $count;
    }

    /**
     * Scope to only published posts.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope to only posts that are publicly visible.
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('visibility', PostVisibility::Public);
    }

    /**
     * Scope to search posts by title, excerpt or content.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = mb_strtolower(trim($term));

        return $query->where(function (Builder $q) use ($term): void {
            $q->whereRaw('LOWER(title) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(excerpt) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(content) LIKE ?', ["%{$term}%"]);
        });
    }

    /**
     * Scope to search posts, their category, tags and author.
     */
    public function scopeAdvancedSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = mb_strtolower(trim($term));

        return $query->where(function (Builder $q) use ($term): void {
            $q->whereRaw('LOWER(title) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(excerpt) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(content) LIKE ?', ["%{$term}%"])
                ->orWhereHas('category', fn (Builder $c) => $c->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]))
                ->orWhereHas('tags', fn (Builder $t) => $t->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]))
                ->orWhereHas('author', fn (Builder $a) => $a
                    ->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(username) LIKE ?', ["%{$term}%"]));
        });
    }

    /**
     * Scope to only posts from the given category slug.
     */
    public function scopeOfCategory(Builder $query, ?string $slug): Builder
    {
        return blank($slug)
            ? $query
            : $query->whereHas('category', fn (Builder $q) => $q->where('categories.slug', $slug));
    }

    /**
     * Scope to only posts carrying the given tag slug.
     */
    public function scopeOfTag(Builder $query, ?string $slug): Builder
    {
        return blank($slug)
            ? $query
            : $query->whereHas('tags', fn (Builder $q) => $q->where('tags.slug', $slug));
    }

    /**
     * Scope to only posts written by the given author username.
     */
    public function scopeOfAuthor(Builder $query, ?string $username): Builder
    {
        return blank($username)
            ? $query
            : $query->whereHas('author', fn (Builder $q) => $q->where('users.username', $username));
    }

    /**
     * Scope to order posts by a named sort option.
     */
    public function scopeSorted(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'oldest' => $query->orderBy('published_at'),
            'popular' => $query->orderByDesc('view_count'),
            'title' => $query->orderBy('title'),
            default => $query->orderByDesc('published_at'),
        };
    }

    /**
     * Determine whether the post is currently published.
     */
    public function isPublished(): bool
    {
        return $this->status === PostStatus::Published->value
            && $this->published_at !== null
            && $this->published_at->lte(now());
    }

    /**
     * Determine whether the post is public.
     */
    public function isPublic(): bool
    {
        return $this->visibility === PostVisibility::Public->value;
    }

    /**
     * The human friendly status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return PostStatus::from($this->status)->label();
    }

    /**
     * The human friendly visibility label.
     */
    public function getVisibilityLabelAttribute(): string
    {
        return PostVisibility::from($this->visibility)->label();
    }

    /**
     * The badge variant for the current status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return PostStatus::from($this->status)->badge();
    }

    /**
     * The badge variant for the current visibility.
     */
    public function getVisibilityBadgeAttribute(): string
    {
        return PostVisibility::from($this->visibility)->badge();
    }

    /**
     * The URL to view the post on the public site.
     */
    public function getUrlAttribute(): string
    {
        return route('blog.show', $this->slug);
    }

    /**
     * The public URL for the featured image.
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (! $this->featured_image) {
            return null;
        }

        return asset('storage/'.$this->featured_image);
    }

    /**
     * The public URL for the thumbnail.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (! $this->thumbnail) {
            return null;
        }

        return asset('storage/'.$this->thumbnail);
    }
}
