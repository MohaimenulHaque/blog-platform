<?php

namespace App\Services;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class PostService
{
    public function __construct(
        protected SlugService $slugs,
        protected HtmlSanitizer $sanitizer,
    ) {
    }

    /**
     * Create a post from validated input.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, int $authorId): Post
    {
        $post = new Post($this->prepareData($data, authorId: $authorId));

        $this->applyTransitions($post, $data);

        $post->save();

        $this->syncTags($post, $data['tags'] ?? []);

        ContentCache::flush();

        return $post;
    }

    /**
     * Update an existing post from validated input.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Post $post, array $data): Post
    {
        $post->fill($this->prepareData($data, $post));

        $this->applyTransitions($post, $data);

        $post->save();

        $this->syncTags($post, $data['tags'] ?? []);

        ContentCache::flush();

        return $post;
    }

    /**
     * Publish a post immediately.
     */
    public function publish(Post $post): Post
    {
        $wasPublished = $post->isPublished();

        $post->forceFill([
            'status' => PostStatus::Published->value,
            'published_at' => now(),
            'scheduled_at' => null,
        ])->save();

        if (! $wasPublished) {
            \Illuminate\Support\Facades\Notification::send(
                \App\Notifications\PostPublishedNotification::recipients($post),
                new \App\Notifications\PostPublishedNotification($post),
            );
        }

        ContentCache::flush();

        return $post;
    }

    /**
     * Unpublish a post, returning it to the draft state.
     */
    public function unpublish(Post $post): Post
    {
        $post->forceFill([
            'status' => PostStatus::Draft->value,
            'published_at' => null,
            'scheduled_at' => null,
        ])->save();

        ContentCache::flush();

        return $post;
    }

    /**
     * Schedule a post to be published at a given date.
     */
    public function schedule(Post $post, string $scheduledAt): Post
    {
        $scheduledAt = Carbon::parse($scheduledAt);

        if ($scheduledAt->isPast()) {
            return $this->publish($post);
        }

        $post->forceFill([
            'status' => PostStatus::Scheduled->value,
            'scheduled_at' => $scheduledAt,
            'published_at' => null,
        ])->save();

        ContentCache::flush();

        return $post;
    }

    /**
     * Archive a post.
     */
    public function archive(Post $post): Post
    {
        $post->forceFill(['status' => PostStatus::Archived->value])->save();

        ContentCache::flush();

        return $post;
    }

    /**
     * Move a post back to the draft state.
     */
    public function draft(Post $post): Post
    {
        $post->forceFill([
            'status' => PostStatus::Draft->value,
            'published_at' => null,
            'scheduled_at' => null,
        ])->save();

        ContentCache::flush();

        return $post;
    }

    /**
     * Store the featured image and thumbnail for a post, deleting replaced files.
     *
     * @param  array<string, mixed>  $data
     */
    public function storeImages(Post $post, array $data): void
    {
        $dirs = config('blog.images');

        if (($path = Arr::get($data, 'featured_image_path'))) {
            $post->featured_image = $path;
        } elseif (($file = Arr::get($data, 'featured_image')) instanceof UploadedFile) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }

            $post->featured_image = $file->store($dirs['featured_dir'], 'public');
        }

        if (($path = Arr::get($data, 'thumbnail_path'))) {
            $post->thumbnail = $path;
        } elseif (($file = Arr::get($data, 'thumbnail')) instanceof UploadedFile) {
            if ($post->thumbnail) {
                Storage::disk('public')->delete($post->thumbnail);
            }

            $post->thumbnail = $file->store($dirs['thumbnail_dir'], 'public');
        }

        $post->save();
    }

    /**
     * Normalise and prepare post data for persistence.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function prepareData(array $data, ?Post $post = null, ?int $authorId = null): array
    {
        $fillable = [
            'title', 'excerpt', 'content', 'category_id', 'visibility',
            'meta_title', 'meta_description', 'meta_keywords', 'canonical_url',
            'og_title', 'og_description',
        ];

        $prepared = Arr::only($data, $fillable);

        $prepared['slug'] = $this->slugs->unique(
            $data['title'],
            Post::class,
            $post?->id,
            $data['slug'] ?? null
        );

        $prepared['content'] = $this->sanitizer->sanitize($data['content'] ?? null);

        $prepared['excerpt'] = blank($data['excerpt'] ?? null)
            ? $this->generateExcerpt($prepared['content'])
            : trim($data['excerpt']);

        $prepared['reading_time'] = $this->calculateReadingTime($prepared['content']);

        if ($authorId) {
            $prepared['author_id'] = $authorId;
        }

        $ogImage = Arr::get($data, 'og_image');

        if ($ogImage instanceof UploadedFile) {
            $prepared['og_image'] = $ogImage->store(config('blog.images.editor_dir'), 'public');
        } elseif ($ogImage) {
            $prepared['og_image'] = $ogImage;
        }

        return $prepared;
    }

    /**
     * Resolve the status and timestamps for a post.
     *
     * @param  array<string, mixed>  $data
     */
    protected function applyTransitions(Post $post, array $data): void
    {
        $status = PostStatus::tryFrom($data['status'] ?? '') ?? PostStatus::Draft;

        $scheduledAt = ! blank($data['scheduled_at'] ?? null)
            ? Carbon::parse($data['scheduled_at'])
            : null;

        $post->status = $status->value;

        switch ($status) {
            case PostStatus::Published:
                $post->published_at = $post->published_at ?: now();
                $post->scheduled_at = null;
                break;

            case PostStatus::Scheduled:
                if (! $scheduledAt) {
                    $post->status = PostStatus::Draft->value;
                    $post->published_at = null;
                    $post->scheduled_at = null;
                    break;
                }

                if ($scheduledAt->isPast()) {
                    $post->status = PostStatus::Published->value;
                    $post->published_at = $scheduledAt;
                    $post->scheduled_at = null;
                    break;
                }

                $post->published_at = null;
                $post->scheduled_at = $scheduledAt;
                break;

            default:
                $post->published_at = null;
                $post->scheduled_at = null;
                break;
        }
    }

    /**
     * Synchronise the tags attached to a post.
     *
     * @param  array<int, int>  $tagIds
     */
    protected function syncTags(Post $post, array $tagIds): void
    {
        $post->tags()->sync(collect($tagIds)->filter()->unique()->values()->all());
    }

    /**
     * Calculate the reading time in minutes from the post content.
     */
    public function calculateReadingTime(?string $content): int
    {
        $wordsPerMinute = max(1, (int) config('blog.reading_time.words_per_minute', 200));
        $minMinutes = max(1, (int) config('blog.reading_time.min_minutes', 1));

        $words = str_word_count($this->stripHtml($content));

        if ($words <= 0) {
            return $minMinutes;
        }

        return max($minMinutes, (int) ceil($words / $wordsPerMinute));
    }

    /**
     * Generate a fallback excerpt from the post content.
     */
    public function generateExcerpt(?string $content, int $length = 160): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $this->stripHtml($content)));

        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $length - 1)).'…';
    }

    /**
     * Strip all HTML tags from the given content.
     */
    protected function stripHtml(?string $content): string
    {
        if (blank($content)) {
            return '';
        }

        return trim((string) preg_replace('/\s+/u', ' ', strip_tags($content)));
    }
}
