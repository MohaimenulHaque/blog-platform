<?php

namespace Database\Factories;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'user_id' => User::factory(),
            'parent_id' => null,
            'body' => fake()->paragraph(),
            'status' => CommentStatus::Approved->value,
            'likes_count' => 0,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => CommentStatus::Approved->value,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => CommentStatus::Pending->value,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => CommentStatus::Rejected->value,
        ]);
    }

    public function spam(): static
    {
        return $this->state(fn () => [
            'status' => CommentStatus::Spam->value,
        ]);
    }
}
