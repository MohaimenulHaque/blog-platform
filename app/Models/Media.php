<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'original_name',
        'mime_type',
        'extension',
        'size',
        'path',
        'alt_text',
        'collection',
        'width',
        'height',
    ];

    /**
     * The user that uploaded the media item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to search media by original name or alt text.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = mb_strtolower(trim($term));

        return $query->where(function (Builder $q) use ($term): void {
            $q->whereRaw('LOWER(original_name) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(COALESCE(name, "")) LIKE ?', ["%{$term}%"])
                ->orWhereRaw('LOWER(COALESCE(alt_text, "")) LIKE ?', ["%{$term}%"]);
        });
    }

    /**
     * The public URL for the media item.
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/'.$this->path);
    }

    /**
     * Whether the media item is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }
}
