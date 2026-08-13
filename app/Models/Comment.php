<?php

namespace App\Models;

use App\Enums\CommentStatus;
use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'body',
        'status',
        'likes_count',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'likes_count' => 'integer',
        ];
    }

    /**
     * The post the comment belongs to.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * The user who wrote the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The comment this comment is replying to.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * The replies to this comment.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    /**
     * The likes on the comment.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(CommentLike::class);
    }

    /**
     * Scope to only approved comments.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', CommentStatus::Approved->value);
    }

    /**
     * Scope to only pending comments.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', CommentStatus::Pending->value);
    }

    /**
     * Scope to search comments by body, author name or post title.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = mb_strtolower(trim($term));

        return $query->where(function (Builder $q) use ($term): void {
            $q->whereRaw('LOWER(body) LIKE ?', ["%{$term}%"])
                ->orWhereHas('user', fn (Builder $u) => $u->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]))
                ->orWhereHas('post', fn (Builder $p) => $p->whereRaw('LOWER(title) LIKE ?', ["%{$term}%"]));
        });
    }

    /**
     * Whether the comment has been approved.
     */
    public function isApproved(): bool
    {
        return $this->status === CommentStatus::Approved->value;
    }

    /**
     * Whether the comment is currently pending.
     */
    public function isPending(): bool
    {
        return $this->status === CommentStatus::Pending->value;
    }

    /**
     * Whether the given user has liked the comment.
     */
    public function isLikedBy(?User $user): bool
    {
        return $user !== null && $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * The human friendly status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return CommentStatus::from($this->status)->label();
    }

    /**
     * The badge variant for the current status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return CommentStatus::from($this->status)->badge();
    }
}
