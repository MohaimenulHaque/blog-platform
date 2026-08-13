<?php

namespace Tests\Feature\Blog;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PublishScheduledPostsTest extends TestCase
{
    use RefreshDatabase;

    public function test_due_scheduled_posts_are_published(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->create([
            'author_id' => $author->id,
            'status' => PostStatus::Scheduled->value,
            'scheduled_at' => now()->subMinute(),
            'published_at' => null,
        ]);

        $this->artisan('posts:publish-scheduled')
            ->expectsOutput('Published 1 scheduled post(s).')
            ->assertExitCode(0);

        $post->refresh();

        $this->assertSame(PostStatus::Published->value, $post->status);
        $this->assertNotNull($post->published_at);
        $this->assertNull($post->scheduled_at);
    }

    public function test_future_scheduled_posts_are_left_untouched(): void
    {
        $author = User::factory()->author()->create();
        $future = Post::factory()->create([
            'author_id' => $author->id,
            'status' => PostStatus::Scheduled->value,
            'scheduled_at' => now()->addDays(2),
            'published_at' => null,
        ]);

        $this->artisan('posts:publish-scheduled')
            ->expectsOutput('Published 0 scheduled post(s).')
            ->assertExitCode(0);

        $this->assertSame(PostStatus::Scheduled->value, $future->fresh()->status);
    }

    public function test_draft_posts_are_not_published_by_the_command(): void
    {
        $author = User::factory()->author()->create();
        $draft = Post::factory()->draft()->create(['author_id' => $author->id]);

        $this->artisan('posts:publish-scheduled')->assertExitCode(0);

        $this->assertSame(PostStatus::Draft->value, $draft->fresh()->status);
    }

    public function test_scheduled_post_is_published_on_the_due_date(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->create([
            'author_id' => $author->id,
            'status' => PostStatus::Scheduled->value,
            'scheduled_at' => Carbon::now()->addDays(3),
            'published_at' => null,
        ]);

        $this->travelTo(Carbon::now()->addDays(3)->addMinute());

        $this->artisan('posts:publish-scheduled')
            ->expectsOutput('Published 1 scheduled post(s).')
            ->assertExitCode(0);

        $this->assertSame(PostStatus::Published->value, $post->fresh()->status);
    }
}
