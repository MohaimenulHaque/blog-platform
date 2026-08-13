<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'bio',
        'avatar',
        'designation',
        'website',
        'social_links',
        'password',
        'role_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'social_links' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope to only active users.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to search users by name, email or username.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = mb_strtolower(trim($term));

        return $query->where(function (Builder $q) use ($term): void {
            $q->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(email) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(COALESCE(username, "")) LIKE ?', ["%{$term}%"]);
        });
    }

    /**
     * Determine whether the user account is active.
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * The user's primary role.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Every role the user has (scalable many-to-many).
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withPivot('primary');
    }

    /**
     * Determine whether the user has the given role.
     */
    public function hasRole(string $slug): bool
    {
        return $this->role?->slug === $slug
            || $this->roles()->where('roles.slug', $slug)->exists();
    }

    /**
     * Determine whether the user has any of the given roles.
     *
     * @param  array<int, string>  $slugs
     */
    public function hasAnyRole(array $slugs): bool
    {
        return collect($slugs)->contains(fn (string $slug) => $this->hasRole($slug));
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isEditor(): bool
    {
        return $this->hasRole('editor');
    }

    public function isAuthor(): bool
    {
        return $this->hasRole('author');
    }

    public function isUser(): bool
    {
        return $this->hasRole('user');
    }

    /**
     * Assign a role to the user. The first assigned role becomes their primary role.
     */
    public function assignRole(string|Role $role): void
    {
        $role = $role instanceof Role ? $role : Role::firstOrCreate(
            ['slug' => $role],
            ['name' => ucfirst($role), 'description' => null]
        );

        $this->roles()->syncWithoutDetaching([
            $role->id => ['primary' => ! $this->roles()->exists()],
        ]);

        if (! $this->role_id) {
            $this->forceFill(['role_id' => $role->id])->save();
        }
    }

    /**
     * Promote a role to be the user's primary role.
     */
    public function setPrimaryRole(string|Role $role): void
    {
        $role = $role instanceof Role ? $role : Role::firstOrCreate(
            ['slug' => $role],
            ['name' => ucfirst($role), 'description' => null]
        );

        $this->roles()->syncWithoutDetaching([$role->id => ['primary' => true]]);

        $this->roles()
            ->newPivotStatement()
            ->where('user_id', $this->id)
            ->where('role_id', '!=', $role->id)
            ->update(['primary' => false]);

        $this->forceFill(['role_id' => $role->id])->save();
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole(string|Role $role): void
    {
        $role = $role instanceof Role ? $role : Role::where('slug', $role)->first();

        if ($role) {
            $this->roles()->detach($role);

            if ($this->role_id === $role->id) {
                $primary = $this->roles()->wherePivot('primary', true)->first();

                $this->forceFill(['role_id' => $primary?->id])->save();
            }
        }
    }

    /**
     * The posts authored by the user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    /**
     * The published posts authored by the user.
     */
    public function publishedPosts(): HasMany
    {
        return $this->posts()->published()->public();
    }

    /**
     * The comments written by the user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The likes the user has given to posts.
     */
    public function postLikes(): HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    /**
     * The posts the user has liked.
     */
    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_likes')->withTimestamps();
    }

    /**
     * The likes the user has given to comments.
     */
    public function commentLikes(): HasMany
    {
        return $this->hasMany(CommentLike::class);
    }

    /**
     * The bookmarks the user has saved.
     */
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * The posts the user has bookmarked.
     */
    public function bookmarkedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'bookmarks')->withTimestamps();
    }

    /**
     * The user's role display label.
     */
    public function getRoleNameAttribute(): ?string
    {
        return $this->role?->name;
    }

    /**
     * The user's public avatar URL.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        return asset('storage/'.$this->avatar);
    }

    /**
     * The user's handle, falling back to their name-based handle.
     */
    public function getHandleAttribute(): string
    {
        return $this->username ?? '@'.strtolower(str_replace(' ', '', $this->name));
    }

    /**
     * The public author profile URL.
     */
    public function getAuthorUrlAttribute(): string
    {
        return route('authors.show', $this->username ?? $this->id);
    }

    /**
     * The resolved designation, falling back to the primary role name.
     */
    public function getDesignationLabelAttribute(): ?string
    {
        return $this->designation ?? $this->role?->name;
    }

    /**
     * The authors social links, filtered to only populated entries.
     *
     * @return array<string, string>
     */
    public function getSocialLinksAttribute(): array
    {
        $raw = $this->attributes['social_links'] ?? null;
        $decoded = is_string($raw) ? json_decode($raw, true) : $raw;

        return collect(is_array($decoded) ? $decoded : [])
            ->filter(fn ($value) => filled($value))
            ->toArray();
    }
}
