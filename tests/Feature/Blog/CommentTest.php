<?php

namespace Tests\Feature\Blog;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\NewCommentNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_when_commenting(): void
    {
        $post = Post::factory()->create();

        $this->post(route('posts.comments.store', $post), ['body' => 'A nice comment.'])
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_post_a_comment(): void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('posts.comments.store', $post), ['body' => 'What a great article!'])
            ->assertOk()
            ->assertJson([
                'status' => 'pending',
                'message' => 'Comment submitted and awaiting approval.',
            ]);

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'body' => 'What a great article!',
            'status' => CommentStatus::Pending->value,
        ]);
    }

    public function test_comment_body_is_validated(): void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('posts.comments.store', $post), ['body' => 'x'])
            ->assertSessionHasErrors('body');
    }

    public function test_comments_are_published_immediately_when_auto_approve_is_enabled(): void
    {
        config(['blog.comments.auto_approve' => true]);

        $post = Post::factory()->create(['comment_count' => 0]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('posts.comments.store', $post), ['body' => 'Approved instantly.'])
            ->assertOk()
            ->assertJsonPath('status', 'approved')
            ->assertJsonPath('message', 'Comment posted successfully.');

        $comment = Comment::firstOrFail();

        $this->assertEquals(CommentStatus::Approved->value, $comment->status);
        $this->assertEquals(1, $post->fresh()->comment_count);
    }

    public function test_only_approved_comments_are_shown_on_the_blog(): void
    {
        $post = Post::factory()->create();
        Comment::factory()->pending()->create(['post_id' => $post->id, 'body' => 'Hidden comment']);
        Comment::factory()->approved()->create(['post_id' => $post->id, 'body' => 'Visible comment']);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('Visible comment')
            ->assertDontSee('Hidden comment');
    }

    public function test_authenticated_user_can_reply_to_an_approved_comment(): void
    {
        $post = Post::factory()->create();
        $parent = Comment::factory()->approved()->create(['post_id' => $post->id]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('posts.comments.store', $post), [
                'body' => 'A helpful reply.',
                'parent_id' => $parent->id,
            ])
            ->assertOk();

        $this->assertDatabaseHas('comments', [
            'parent_id' => $parent->id,
            'body' => 'A helpful reply.',
        ]);
    }

    public function test_users_cannot_reply_to_a_pending_comment(): void
    {
        $post = Post::factory()->create();
        $parent = Comment::factory()->pending()->create(['post_id' => $post->id]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('posts.comments.store', $post), [
                'body' => 'Trying to reply to a hidden comment.',
                'parent_id' => $parent->id,
            ])
            ->assertSessionHasErrors('parent_id');
    }

    public function test_users_cannot_reply_to_a_reply(): void
    {
        $post = Post::factory()->create();
        $topLevel = Comment::factory()->approved()->create(['post_id' => $post->id]);
        $reply = Comment::factory()->approved()->create(['post_id' => $post->id, 'parent_id' => $topLevel->id]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('posts.comments.store', $post), [
                'body' => 'Threading too deep.',
                'parent_id' => $reply->id,
            ])
            ->assertSessionHasErrors('parent_id');
    }

    public function test_users_cannot_reply_to_a_comment_on_a_different_post(): void
    {
        $postA = Post::factory()->create();
        $postB = Post::factory()->create();
        $parent = Comment::factory()->approved()->create(['post_id' => $postA->id]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('posts.comments.store', $postB), [
                'body' => 'Wrong thread.',
                'parent_id' => $parent->id,
            ])
            ->assertSessionHasErrors('parent_id');
    }

    public function test_comment_author_is_notified(): void
    {
        Notification::fake();

        $post = Post::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('posts.comments.store', $post), ['body' => 'Heads up, author!'])
            ->assertOk();

        Notification::assertSentTo($post->author, NewCommentNotification::class);
    }

    public function test_author_is_not_notified_when_commenting_on_their_own_post(): void
    {
        Notification::fake();

        $post = Post::factory()->create();

        $this->actingAs($post->author)
            ->postJson(route('posts.comments.store', $post), ['body' => 'My own post.'])
            ->assertOk();

        Notification::assertNothingSent();
    }

    public function test_author_can_update_their_own_comment(): void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();
        $comment = Comment::factory()->approved()->create(['post_id' => $post->id, 'user_id' => $user->id]);

        $this->actingAs($user)
            ->patchJson(route('comments.update', $comment), ['body' => 'Updated body text.'])
            ->assertOk();

        $this->assertEquals('Updated body text.', $comment->fresh()->body);
    }

    public function test_updating_an_approved_comment_returns_it_to_pending(): void
    {
        $post = Post::factory()->create(['comment_count' => 1]);
        $user = User::factory()->create();
        $comment = Comment::factory()->approved()->create(['post_id' => $post->id, 'user_id' => $user->id]);

        $this->actingAs($user)
            ->patchJson(route('comments.update', $comment), ['body' => 'Edited body text.'])
            ->assertOk();

        $this->assertEquals(CommentStatus::Pending->value, $comment->fresh()->status);
        $this->assertEquals(0, $post->fresh()->comment_count);
    }

    public function test_users_cannot_update_someone_elses_comment(): void
    {
        $post = Post::factory()->create();
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $comment = Comment::factory()->approved()->create(['post_id' => $post->id, 'user_id' => $owner->id]);

        $this->actingAs($other)
            ->patchJson(route('comments.update', $comment), ['body' => 'Malicious edit.'])
            ->assertForbidden();
    }

    public function test_author_can_delete_their_own_comment(): void
    {
        $post = Post::factory()->create(['comment_count' => 1]);
        $user = User::factory()->create();
        $comment = Comment::factory()->approved()->create(['post_id' => $post->id, 'user_id' => $user->id]);

        $this->actingAs($user)
            ->deleteJson(route('comments.destroy', $comment))
            ->assertOk();

        $this->assertSoftDeleted('comments', ['id' => $comment->id]);
        $this->assertEquals(0, $post->fresh()->comment_count);
    }

    public function test_users_cannot_delete_someone_elses_comment(): void
    {
        $post = Post::factory()->create();
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $comment = Comment::factory()->approved()->create(['post_id' => $post->id, 'user_id' => $owner->id]);

        $this->actingAs($other)
            ->deleteJson(route('comments.destroy', $comment))
            ->assertForbidden();
    }

    public function test_comment_page_shows_count_and_guest_login_prompt(): void
    {
        $post = Post::factory()->create(['comment_count' => 1]);
        Comment::factory()->approved()->create(['post_id' => $post->id, 'body' => 'Only approved count.']);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('Comments')
            ->assertSee('(1)')
            ->assertSee('Log in')
            ->assertSee('to join the conversation.');
    }
}
