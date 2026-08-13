<?php

namespace Tests\Feature\Blog;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCommentModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_editors_can_access_the_comment_queue(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.comments.index'))
            ->assertOk();
    }

    public function test_admins_can_access_the_comment_queue(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.comments.index'))
            ->assertOk();
    }

    public function test_authors_cannot_access_the_comment_queue(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->get(route('admin.comments.index'))
            ->assertForbidden();
    }

    public function test_regular_users_cannot_access_the_comment_queue(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.comments.index'))
            ->assertForbidden();
    }

    public function test_editors_can_approve_a_pending_comment(): void
    {
        $post = Post::factory()->create(['comment_count' => 0]);
        $comment = Comment::factory()->pending()->create(['post_id' => $post->id]);
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->patch(route('admin.comments.status', $comment), ['status' => 'approved'])
            ->assertRedirect()
            ->assertSessionHas('status', 'Comment marked as Approved.');

        $this->assertEquals(CommentStatus::Approved->value, $comment->fresh()->status);
        $this->assertEquals(1, $post->fresh()->comment_count);
    }

    public function test_editors_can_reject_a_comment(): void
    {
        $post = Post::factory()->create(['comment_count' => 1]);
        $comment = Comment::factory()->approved()->create(['post_id' => $post->id]);
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->patch(route('admin.comments.status', $comment), ['status' => 'rejected'])
            ->assertRedirect();

        $this->assertEquals(CommentStatus::Rejected->value, $comment->fresh()->status);
        $this->assertEquals(0, $post->fresh()->comment_count);
    }

    public function test_editors_can_mark_a_comment_as_spam(): void
    {
        $comment = Comment::factory()->pending()->create();
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->patch(route('admin.comments.status', $comment), ['status' => 'spam'])
            ->assertRedirect();

        $this->assertEquals(CommentStatus::Spam->value, $comment->fresh()->status);
    }

    public function test_editors_can_delete_a_comment(): void
    {
        $post = Post::factory()->create(['comment_count' => 1]);
        $comment = Comment::factory()->approved()->create(['post_id' => $post->id]);
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->delete(route('admin.comments.destroy', $comment))
            ->assertRedirect();

        $this->assertSoftDeleted('comments', ['id' => $comment->id]);
        $this->assertEquals(0, $post->fresh()->comment_count);
    }

    public function test_comment_status_must_be_valid(): void
    {
        $comment = Comment::factory()->pending()->create();
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->patch(route('admin.comments.status', $comment), ['status' => 'nonsense'])
            ->assertSessionHasErrors('status');
    }

    public function test_comment_queue_supports_searching(): void
    {
        $comment = Comment::factory()->pending()->create(['body' => 'Unique searchable phrase']);
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.comments.index', ['q' => 'searchable']))
            ->assertOk()
            ->assertSee('Unique searchable phrase');
    }

    public function test_comment_queue_supports_status_filtering(): void
    {
        Comment::factory()->pending()->create(['body' => 'Pending item']);
        Comment::factory()->approved()->create(['body' => 'Approved item']);
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.comments.index', ['status' => 'approved']))
            ->assertOk()
            ->assertSee('Approved item')
            ->assertDontSee('Pending item');
    }
}
