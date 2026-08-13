<?php

namespace Tests\Feature\Blog;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\CommentApprovedNotification;
use App\Notifications\NewCommentNotification;
use App\Notifications\PostPublishedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_when_viewing_notifications(): void
    {
        $this->get(route('notifications.index'))
            ->assertRedirect(route('login'));
    }

    public function test_notifications_page_lists_notifications(): void
    {
        $post = Post::factory()->create(['title' => 'Commented Story']);
        $user = User::factory()->create();

        $user->notify(new NewCommentNotification(
            Comment::factory()->approved()->create(['post_id' => $post->id, 'user_id' => $user->id])
        ));

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('New comment on your post')
            ->assertSee('Commented Story');
    }

    public function test_commenting_notifies_the_post_author(): void
    {
        Notification::fake();

        $post = Post::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('posts.comments.store', $post), ['body' => 'Great read.'])
            ->assertOk();

        Notification::assertSentTo($post->author, NewCommentNotification::class);
    }

    public function test_approving_a_comment_notifies_the_commenter(): void
    {
        Notification::fake();

        $post = Post::factory()->create();
        $commenter = User::factory()->create();
        $editor = User::factory()->editor()->create();
        $comment = Comment::factory()->pending()->create(['post_id' => $post->id, 'user_id' => $commenter->id]);

        $this->actingAs($editor)
            ->patch(route('admin.comments.status', $comment), ['status' => 'approved'])
            ->assertRedirect();

        Notification::assertSentTo($commenter, CommentApprovedNotification::class);
    }

    public function test_publishing_a_post_notifies_admins_and_editors(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();
        $author = User::factory()->author()->create();
        $post = Post::factory()->draft()->create(['author_id' => $author->id]);

        $this->actingAs($admin)
            ->post(route('admin.posts.publish', $post))
            ->assertRedirect();

        Notification::assertSentTo([$admin, $editor], PostPublishedNotification::class);
        Notification::assertNotSentTo($author, PostPublishedNotification::class);
    }

    public function test_marking_a_notification_as_read(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $user->notify(new NewCommentNotification(
            Comment::factory()->approved()->create(['post_id' => $post->id, 'user_id' => $user->id])
        ));

        $notification = $user->notifications()->firstOrFail();

        $this->assertNull($notification->read_at);

        $this->actingAs($user)
            ->post(route('notifications.read', $notification->id))
            ->assertRedirect();

        $this->assertNotNull($user->notifications()->first()->read_at);
    }

    public function test_marking_all_notifications_as_read(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $user->notify(new NewCommentNotification(
            Comment::factory()->approved()->create(['post_id' => $post->id, 'user_id' => $user->id])
        ));
        $user->notify(new NewCommentNotification(
            Comment::factory()->approved()->create(['post_id' => $post->id, 'user_id' => $user->id])
        ));

        $this->actingAs($user)
            ->post(route('notifications.read-all'))
            ->assertRedirect();

        $this->assertSame(0, $user->fresh()->unreadNotifications->count());
        $this->assertSame(2, $user->fresh()->notifications()->whereNotNull('read_at')->count());
    }

    public function test_users_cannot_mark_other_users_notifications_as_read(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->create();

        $owner->notify(new NewCommentNotification(
            Comment::factory()->approved()->create(['post_id' => $post->id, 'user_id' => $owner->id])
        ));

        $notification = $owner->notifications()->firstOrFail();

        $this->actingAs($other)
            ->post(route('notifications.read', $notification->id))
            ->assertNotFound();
    }

    public function test_notifications_page_shows_an_empty_state(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee("You're all caught up");
    }
}
