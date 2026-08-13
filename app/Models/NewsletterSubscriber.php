<?php

namespace App\Models;

use Database\Factories\NewsletterSubscriberFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    /** @use HasFactory<NewsletterSubscriberFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'name',
        'token',
        'subscribed',
        'unsubscribed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subscribed' => 'boolean',
            'unsubscribed_at' => 'datetime',
        ];
    }

    /**
     * Scope to only active subscribers.
     */
    public function scopeSubscribed(Builder $query): Builder
    {
        return $query->where('subscribed', true);
    }

    /**
     * Scope to search subscribers by email or name.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = mb_strtolower(trim($term));

        return $query->where(function (Builder $q) use ($term): void {
            $q->whereRaw('LOWER(email) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(COALESCE(name, "")) LIKE ?', ["%{$term}%"]);
        });
    }

    /**
     * Whether the subscriber is currently active.
     */
    public function isSubscribed(): bool
    {
        return $this->subscribed;
    }
}
