<?php

namespace Tests\Feature\Blog;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_when_viewing_bookmarks(): void
    {
        $this->get(route('bookmarks.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_bookmark_a_post(): void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('posts.bookmark', $post))
            ->assertOk()
            ->assertJson(['bookmarked' => true]);

        $this->assertDatabaseHas('bookmarks', [
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_user_can_remove_a_bookmark(): void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();

        $post->bookmarks()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->postJson(route('posts.bookmark', $post))
            ->assertOk()
            ->assertJson(['bookmarked' => false]);

        $this->assertDatabaseMissing('bookmarks', [
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_bookmarking_twice_does_not_duplicate(): void
    {
        $post = Post::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('posts.bookmark', $post))
            ->assertOk()
            ->assertJson(['bookmarked' => true]);

        $this->actingAs($user)
            ->postJson(route('posts.bookmark', $post))
            ->assertOk()
            ->assertJson(['bookmarked' => false]);

        $this->assertDatabaseCount('bookmarks', 0);
    }

    public function test_bookmarks_page_lists_saved_posts(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['title' => 'Saved For Later']);
        $user->bookmarks()->create(['post_id' => $post->id]);

        $this->actingAs($user)
            ->get(route('bookmarks.index'))
            ->assertOk()
            ->assertSee('Your bookmarks')
            ->assertSee('Saved For Later');
    }

    public function test_bookmarks_page_shows_an_empty_state(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('bookmarks.index'))
            ->assertOk()
            ->assertSee('No bookmarks yet');
    }

    public function test_users_only_see_their_own_bookmarks(): void
    {
        $other = User::factory()->create();
        $post = Post::factory()->create(['title' => 'Someone Elses Save']);
        $other->bookmarks()->create(['post_id' => $post->id]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('bookmarks.index'))
            ->assertOk()
            ->assertDontSee('Someone Elses Save');
    }
}
