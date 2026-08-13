<?php

namespace Tests\Feature\Blog;

use App\Enums\CommentStatus;
use App\Enums\PostStatus;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_renders(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Welcome back')
            ->assertSee('Published posts');
    }

    public function test_editors_cannot_access_the_admin_dashboard(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_dashboard_displays_content_statistics(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->author()->create();
        Post::factory()->count(3)->create(['author_id' => $author->id]);
        Post::factory()->count(2)->draft()->create(['author_id' => $author->id]);
        Post::factory()->count(1)->scheduled()->create(['author_id' => $author->id]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Published posts')
            ->assertSee('draft', false)
            ->assertSee('scheduled', false)
            ->assertSee('New post');
    }

    public function test_dashboard_charts_are_built_from_real_data(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->author()->create();

        Post::factory()->count(4)->create([
            'author_id' => $author->id,
            'published_at' => now()->startOfMonth(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();

        $content = $response->getContent();
        $this->assertStringContainsString('chart-posts-published', $content);
        $this->assertStringContainsString('chart-categories', $content);
        $this->assertStringContainsString('chart-users', $content);
    }

    public function test_pending_comment_count_appears_on_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create();
        Comment::factory()->count(2)->pending()->create(['post_id' => $post->id]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('awaiting moderation');
    }

    public function test_dashboard_shows_recent_posts_and_comments(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create(['title' => 'Freshly Published Story']);
        Comment::factory()->approved()->create(['post_id' => $post->id, 'body' => 'A thoughtful reader response.']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Freshly Published Story')
            ->assertSee('A thoughtful reader response.');
    }

    public function test_dashboard_publishes_chart_json_data(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('postsPublished', false);
    }
}
