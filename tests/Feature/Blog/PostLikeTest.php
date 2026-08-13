<?php

namespace Tests\Feature\Blog;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostLikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_when_liking(): void
    {
        $post = Post::factory()->create();

        $this->post(route('posts.like', $post))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_like_a_post(): void
    {
        $post = Post::factory()->create(['like_count' => 0]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('posts.like', $post))
            ->assertOk()
            ->assertJson(['liked' => true, 'count' => 1]);

        $this->assertDatabaseHas('post_likes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals(1, $post->fresh()->like_count);
    }

    public function test_authenticated_user_can_unlike_a_post(): void
    {
        $post = Post::factory()->create(['like_count' => 1]);
        $user = User::factory()->create();

        $post->likes()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->postJson(route('posts.like', $post))
            ->assertOk()
            ->assertJson(['liked' => false, 'count' => 0]);

        $this->assertDatabaseMissing('post_likes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals(0, $post->fresh()->like_count);
    }

    public function test_liking_a_post_twice_does_not_duplicate_the_like(): void
    {
        $post = Post::factory()->create(['like_count' => 0]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('posts.like', $post))
            ->assertOk()
            ->assertJson(['liked' => true, 'count' => 1]);

        $this->actingAs($user)
            ->postJson(route('posts.like', $post))
            ->assertOk()
            ->assertJson(['liked' => false, 'count' => 0]);

        $this->assertDatabaseCount('post_likes', 0);
        $this->assertEquals(0, $post->fresh()->like_count);
    }

    public function test_likes_from_different_users_accumulate(): void
    {
        $post = Post::factory()->create(['like_count' => 0]);
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $this->actingAs($userA)->postJson(route('posts.like', $post))->assertOk();
        $this->actingAs($userB)->postJson(route('posts.like', $post))->assertOk();

        $this->assertEquals(2, $post->fresh()->like_count);
        $this->assertDatabaseCount('post_likes', 2);
    }

    public function test_blog_show_displays_like_and_bookmark_buttons(): void
    {
        $post = Post::factory()->create(['like_count' => 3]);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('Like')
            ->assertSee('Save')
            ->assertSee('Copy link')
            ->assertSee('3');
    }
}
