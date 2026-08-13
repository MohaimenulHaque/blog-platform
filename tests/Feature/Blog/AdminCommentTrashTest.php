<?php

namespace Tests\Feature\Blog;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCommentTrashTest extends TestCase
{
    use RefreshDatabase;

    public function test_trashed_comments_page_is_available_to_content_managers(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.comments.trashed'))
            ->assertOk()
            ->assertSee('Trashed comments');
    }

    public function test_authors_cannot_access_trashed_comments(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->get(route('admin.comments.trashed'))
            ->assertForbidden();
    }

    public function test_deleted_comments_appear_in_the_trash(): void
    {
        $editor = User::factory()->editor()->create();
        $comment = Comment::factory()->approved()->create();
        $comment->delete();

        $this->actingAs($editor)
            ->get(route('admin.comments.trashed'))
            ->assertOk()
            ->assertSee($comment->body);
    }

    public function test_comment_can_be_restored_from_the_trash(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->approved()->create(['post_id' => $post->id]);
        $comment->delete();

        $this->actingAs($editor)
            ->patch(route('admin.comments.restore', $comment))
            ->assertRedirect(route('admin.comments.trashed'))
            ->assertSessionHas('status', 'Comment restored.');

        $this->assertNull($comment->fresh()->deleted_at);
        $this->assertSame($comment->status, CommentStatus::Approved->value);
    }

    public function test_restoring_an_approved_comment_syncs_the_post_count(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->create(['comment_count' => 0]);
        $comment = Comment::factory()->approved()->create(['post_id' => $post->id]);
        $comment->delete();

        $this->actingAs($editor)
            ->patch(route('admin.comments.restore', $comment));

        $this->assertSame(1, $post->fresh()->comment_count);
    }

    public function test_comment_can_be_permanently_deleted(): void
    {
        $editor = User::factory()->editor()->create();
        $comment = Comment::factory()->approved()->create();
        $comment->delete();

        $this->actingAs($editor)
            ->delete(route('admin.comments.force-destroy', $comment))
            ->assertRedirect(route('admin.comments.trashed'))
            ->assertSessionHas('status', 'Comment permanently deleted.');

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_trash_supports_searching(): void
    {
        $editor = User::factory()->editor()->create();
        $keep = Comment::factory()->approved()->create(['body' => 'Keep this one in trash']);
        $other = Comment::factory()->approved()->create(['body' => 'Different comment body']);
        $keep->delete();
        $other->delete();

        $this->actingAs($editor)
            ->get(route('admin.comments.trashed', ['q' => 'Keep this one']))
            ->assertOk()
            ->assertSee('Keep this one')
            ->assertDontSee('Different comment body');
    }
}
