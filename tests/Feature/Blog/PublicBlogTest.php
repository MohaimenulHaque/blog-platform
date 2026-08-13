<?php

namespace Tests\Feature\Blog;

use App\Enums\PostStatus;
use App\Enums\PostVisibility;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicBlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_renders_published_posts(): void
    {
        $post = Post::factory()->create([
            'title' => 'A Wonderful Published Post',
        ]);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('A Wonderful Published Post')
            ->assertSee($post->author->name);
    }

    public function test_blog_index_hides_draft_posts(): void
    {
        Post::factory()->draft()->create([
            'title' => 'Draft Post That Must Not Appear',
        ]);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertDontSee('Draft Post That Must Not Appear');
    }

    public function test_blog_index_hides_private_posts(): void
    {
        Post::factory()->create([
            'title' => 'Private Post That Must Not Appear',
            'visibility' => PostVisibility::Private->value,
        ]);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertDontSee('Private Post That Must Not Appear');
    }

    public function test_blog_show_renders_a_published_post(): void
    {
        $post = Post::factory()->create([
            'title' => 'A Post Worth Reading',
            'content' => '<p>Interesting content that defines the article body.</p>',
        ]);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('A Post Worth Reading')
            ->assertSee('Interesting content that defines the article body.');
    }

    public function test_blog_show_returns_404_for_unpublished_posts(): void
    {
        $draft = Post::factory()->draft()->create();
        $pending = Post::factory()->pending()->create();
        $scheduled = Post::factory()->scheduled()->create();

        $this->get(route('blog.show', $draft->slug))->assertNotFound();
        $this->get(route('blog.show', $pending->slug))->assertNotFound();
        $this->get(route('blog.show', $scheduled->slug))->assertNotFound();
    }

    public function test_blog_show_increments_the_view_count(): void
    {
        $post = Post::factory()->create(['view_count' => 0]);

        $this->get(route('blog.show', $post->slug))->assertOk();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'view_count' => 1,
        ]);
    }

    public function test_blog_index_can_search_posts(): void
    {
        Post::factory()->create(['title' => 'Matching Search Term']);

        $this->get(route('blog.index', ['q' => 'Matching']))
            ->assertOk()
            ->assertSee('Matching Search Term');
    }

    public function test_categories_index_lists_active_categories(): void
    {
        $category = Category::factory()->create(['name' => 'Photography']);

        $this->get(route('categories.index'))
            ->assertOk()
            ->assertSee('Photography');
    }

    public function test_category_show_lists_only_published_posts(): void
    {
        $category = Category::factory()->create();
        Post::factory()->create(['category_id' => $category->id, 'title' => 'Published In Category']);
        Post::factory()->draft()->create(['category_id' => $category->id, 'title' => 'Hidden Draft In Category']);

        $this->get(route('categories.show', $category->slug))
            ->assertOk()
            ->assertSee('Published In Category')
            ->assertDontSee('Hidden Draft In Category');
    }

    public function test_inactive_category_show_returns_404(): void
    {
        $category = Category::factory()->inactive()->create();

        $this->get(route('categories.show', $category->slug))->assertNotFound();
    }

    public function test_tags_index_lists_tags(): void
    {
        Tag::factory()->create(['name' => 'Laravel']);

        $this->get(route('tags.index'))
            ->assertOk()
            ->assertSee('Laravel');
    }

    public function test_tag_show_lists_posts_with_that_tag(): void
    {
        $tag = Tag::factory()->create(['name' => 'Laravel']);
        $post = Post::factory()->create(['title' => 'Laravel Deep Dive']);
        $post->tags()->attach($tag);

        $this->get(route('tags.show', $tag->slug))
            ->assertOk()
            ->assertSee('Laravel Deep Dive');
    }

    public function test_authors_index_lists_authors(): void
    {
        $author = User::factory()->author()->create([
            'name' => 'Alex Rivera',
            'username' => 'alex',
            'designation' => 'Staff Writer',
        ]);

        $this->get(route('authors.index'))
            ->assertOk()
            ->assertSee('Alex Rivera')
            ->assertSee('Staff Writer');
    }

    public function test_author_show_lists_their_published_posts(): void
    {
        $author = User::factory()->author()->create([
            'name' => 'Alex Rivera',
            'username' => 'alex',
            'designation' => 'Staff Writer',
        ]);

        Post::factory()->create(['author_id' => $author->id, 'title' => 'Authored By Alex']);
        Post::factory()->draft()->create(['author_id' => $author->id, 'title' => 'Hidden Draft']);

        $this->get(route('authors.show', 'alex'))
            ->assertOk()
            ->assertSee('Authored By Alex')
            ->assertDontSee('Hidden Draft');
    }

    public function test_author_show_returns_404_for_unknown_username(): void
    {
        $this->get(route('authors.show', 'nobody'))->assertNotFound();
    }

    public function test_draft_posts_never_leak_into_public_lists(): void
    {
        Post::factory()->count(3)->create();
        Post::factory()->count(3)->draft()->create();

        $this->get(route('blog.index'))
            ->assertOk();

        $this->assertSame(
            Post::published()->count(),
            Post::where('status', PostStatus::Published)->count()
        );
    }
}
