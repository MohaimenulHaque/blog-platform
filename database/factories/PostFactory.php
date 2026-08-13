<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Enums\PostVisibility;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(5, false);

        return [
            'uuid' => Str::uuid(),
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->paragraph(),
            'content' => '<p>'.fake()->paragraph(4).'</p><p>'.fake()->paragraph(5).'</p><h2>'.fake()->sentence(3).'</h2><p>'.fake()->paragraph(6).'</p>',
            'featured_image' => null,
            'thumbnail' => null,
            'author_id' => User::factory()->author(),
            'category_id' => Category::factory(),
            'status' => PostStatus::Published->value,
            'visibility' => PostVisibility::Public->value,
            'published_at' => now()->subHours(fake()->numberBetween(1, 72)),
            'scheduled_at' => null,
            'reading_time' => 3,
            'view_count' => 0,
            'like_count' => 0,
            'comment_count' => 0,
            'meta_title' => null,
            'meta_description' => null,
            'meta_keywords' => null,
            'canonical_url' => null,
            'og_title' => null,
            'og_description' => null,
            'og_image' => null,
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Post $post): void {
            $post->tags()->sync(
                \App\Models\Tag::factory()->count(2)->create()->pluck('id')
            );
        });
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => PostStatus::Draft->value,
            'published_at' => null,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => PostStatus::Pending->value,
            'published_at' => null,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'status' => PostStatus::Scheduled->value,
            'scheduled_at' => now()->addDays(3),
            'published_at' => null,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn () => [
            'status' => PostStatus::Archived->value,
        ]);
    }

    public function private(): static
    {
        return $this->state(fn () => [
            'visibility' => PostVisibility::Private->value,
        ]);
    }
}
