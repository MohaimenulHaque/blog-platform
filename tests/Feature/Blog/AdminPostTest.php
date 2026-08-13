<?php

namespace Tests\Feature\Blog;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_can_access_the_posts_index(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->get(route('admin.posts.index'))
            ->assertOk();
    }

    public function test_regular_user_cannot_access_the_posts_index(): void
    {
        $user = User::factory()->withRole('user')->create();

        $this->actingAs($user)
            ->get(route('admin.posts.index'))
            ->assertForbidden();
    }

    public function test_author_can_create_a_post(): void
    {
        $author = User::factory()->author()->create();
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $this->actingAs($author)
            ->post(route('admin.posts.store'), [
                'title' => 'My First Post',
                'content' => '<p>Some rich content that should be long enough for a reading time calculation.</p>',
                'excerpt' => 'A short intro',
                'category_id' => $category->id,
                'tags' => $tags->pluck('id')->all(),
                'status' => PostStatus::Draft->value,
                'visibility' => 'public',
            ])
            ->assertRedirect();

        $post = Post::where('title', 'My First Post')->first();

        $this->assertNotNull($post);
        $this->assertSame($author->id, $post->author_id);
        $this->assertSame('my-first-post', $post->slug);
        $this->assertSame(PostStatus::Draft->value, $post->status);
        $this->assertNotNull($post->uuid);
        $this->assertGreaterThanOrEqual(1, $post->reading_time);
        $this->assertSame($category->id, $post->category_id);
        $this->assertSame($tags->pluck('id')->sort()->values()->all(), $post->tags()->pluck('tags.id')->sort()->values()->all());
    }

    public function test_duplicate_slugs_are_uniquified(): void
    {
        $author = User::factory()->author()->create();
        Post::factory()->create(['title' => 'Same Title', 'slug' => 'same-title']);

        $this->actingAs($author)
            ->post(route('admin.posts.store'), [
                'title' => 'Same Title',
                'status' => PostStatus::Draft->value,
                'visibility' => 'public',
            ])
            ->assertRedirect();

        $this->assertSame(2, Post::where('slug', 'like', 'same-title%')->count());
    }

    public function test_author_can_edit_their_own_draft(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->draft()->create(['author_id' => $author->id]);

        $this->actingAs($author)
            ->patch(route('admin.posts.update', $post), [
                'title' => 'Renamed Post',
                'status' => PostStatus::Draft->value,
                'visibility' => 'public',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Renamed Post',
        ]);
    }

    public function test_author_cannot_edit_someone_elses_post(): void
    {
        $author = User::factory()->author()->create();
        $other = User::factory()->author()->create();
        $post = Post::factory()->draft()->create(['author_id' => $other->id]);

        $this->actingAs($author)
            ->patch(route('admin.posts.update', $post), [
                'title' => 'Hijacked',
                'status' => PostStatus::Draft->value,
                'visibility' => 'public',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => $post->title]);
    }

    public function test_editor_can_edit_any_post(): void
    {
        $editor = User::factory()->editor()->create();
        $author = User::factory()->author()->create();
        $post = Post::factory()->create(['author_id' => $author->id]);

        $this->actingAs($editor)
            ->patch(route('admin.posts.update', $post), [
                'title' => 'Edited By Editor',
                'status' => PostStatus::Published->value,
                'visibility' => 'public',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => 'Edited By Editor']);
    }

    public function test_author_cannot_publish_a_post(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->pending()->create(['author_id' => $author->id]);

        $this->actingAs($author)
            ->post(route('admin.posts.publish', $post))
            ->assertForbidden();

        $this->assertSame(PostStatus::Pending->value, $post->fresh()->status);
    }

    public function test_editor_can_publish_a_post(): void
    {
        $editor = User::factory()->editor()->create();
        $author = User::factory()->author()->create();
        $post = Post::factory()->pending()->create(['author_id' => $author->id]);

        $this->actingAs($editor)
            ->post(route('admin.posts.publish', $post))
            ->assertRedirect();

        $post->refresh();

        $this->assertSame(PostStatus::Published->value, $post->status);
        $this->assertNotNull($post->published_at);
    }

    public function test_editor_can_unpublish_and_archive_a_post(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->create();

        $this->actingAs($editor)->post(route('admin.posts.unpublish', $post))->assertRedirect();
        $this->assertSame(PostStatus::Draft->value, $post->fresh()->status);

        $this->actingAs($editor)->post(route('admin.posts.archive', $post))->assertRedirect();
        $this->assertSame(PostStatus::Archived->value, $post->fresh()->status);
    }

    public function test_scheduling_a_post_in_the_future_marks_it_scheduled(): void
    {
        $editor = User::factory()->editor()->create();
        $post = Post::factory()->draft()->create();

        $this->actingAs($editor)
            ->post(route('admin.posts.schedule', $post), [
                'scheduled_at' => now()->addDays(5)->format('Y-m-d H:i'),
            ])
            ->assertRedirect();

        $post->refresh();

        $this->assertSame(PostStatus::Scheduled->value, $post->status);
        $this->assertNotNull($post->scheduled_at);
    }

    public function test_author_can_delete_their_own_draft_and_restore_it(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->draft()->create(['author_id' => $author->id]);

        $this->actingAs($author)
            ->delete(route('admin.posts.destroy', $post))
            ->assertRedirect();

        $this->assertSoftDeleted('posts', ['id' => $post->id]);

        $this->actingAs($author)
            ->patch(route('admin.posts.restore', $post))
            ->assertRedirect();

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    public function test_author_cannot_delete_a_published_post(): void
    {
        $author = User::factory()->author()->create();
        $post = Post::factory()->create(['author_id' => $author->id]);

        $this->actingAs($author)
            ->delete(route('admin.posts.destroy', $post))
            ->assertForbidden();

        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    public function test_trashed_page_is_only_visible_to_content_managers(): void
    {
        $editor = User::factory()->editor()->create();
        $author = User::factory()->author()->create();
        $user = User::factory()->withRole('user')->create();

        $this->actingAs($editor)->get(route('admin.posts.trashed'))->assertOk();
        $this->actingAs($author)->get(route('admin.posts.trashed'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.posts.trashed'))->assertForbidden();
    }

    public function test_content_is_sanitized_on_create(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->post(route('admin.posts.store'), [
                'title' => 'Sanitized Post',
                'content' => '<p>Hello</p><script>alert("xss")</script><a href="https://example.com">link</a>',
                'status' => PostStatus::Draft->value,
                'visibility' => 'public',
            ])
            ->assertRedirect();

        $post = Post::where('title', 'Sanitized Post')->first();

        $this->assertStringContainsString('<p>Hello</p>', $post->content);
        $this->assertStringNotContainsString('<script>', $post->content);
        $this->assertStringContainsString('<a href="https://example.com">link</a>', $post->content);
    }

    public function test_editor_can_upload_an_image(): void
    {
        Storage::fake('public');

        $editor = User::factory()->editor()->create();

        $response = $this->actingAs($editor)
            ->post(route('admin.uploads.store'), [
                'image' => UploadedFile::fake()->image('photo.jpg', 800, 600),
            ])
            ->assertOk()
            ->assertJsonStructure(['location']);

        $location = $response->json('location');
        $path = str_replace('/storage/', '', (string) parse_url($location, PHP_URL_PATH));

        Storage::disk('public')->assertExists($path);
    }

    public function test_author_cannot_upload_an_image(): void
    {
        Storage::fake('public');

        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->post(route('admin.uploads.store'), [
                'image' => UploadedFile::fake()->image('photo.jpg'),
            ])
            ->assertForbidden();
    }
}
