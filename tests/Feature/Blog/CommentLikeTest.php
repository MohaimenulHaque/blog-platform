<?php

namespace Tests\Feature\Blog;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentLikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_when_liking_a_comment(): void
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->approved()->create(['post_id' => $post->id]);

        $this->post(route('comments.like', $comment))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_like_a_comment(): void
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->approved()->create(['post_id' => $post->id, 'likes_count' => 0]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('comments.like', $comment))
            ->assertOk()
            ->assertJson(['liked' => true, 'count' => 1]);

        $this->assertDatabaseHas('comment_likes', [
            'comment_id' => $comment->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals(1, $comment->fresh()->likes_count);
    }

    public function test_authenticated_user_can_unlike_a_comment(): void
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->approved()->create(['post_id' => $post->id, 'likes_count' => 1]);
        $user = User::factory()->create();

        $comment->likes()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->postJson(route('comments.like', $comment))
            ->assertOk()
            ->assertJson(['liked' => false, 'count' => 0]);

        $this->assertDatabaseMissing('comment_likes', [
            'comment_id' => $comment->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals(0, $comment->fresh()->likes_count);
    }

    public function test_liking_a_comment_twice_does_not_duplicate_the_like(): void
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->approved()->create(['post_id' => $post->id, 'likes_count' => 0]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('comments.like', $comment))
            ->assertOk()
            ->assertJson(['liked' => true, 'count' => 1]);

        $this->actingAs($user)
            ->postJson(route('comments.like', $comment))
            ->assertOk()
            ->assertJson(['liked' => false, 'count' => 0]);

        $this->assertDatabaseCount('comment_likes', 0);
        $this->assertEquals(0, $comment->fresh()->likes_count);
    }
}
