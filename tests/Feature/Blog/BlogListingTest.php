<?php

namespace Tests\Feature\Blog;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_shows_total_post_count(): void
    {
        Post::factory()->count(3)->create();

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('3</strong> posts published', false);
    }

    public function test_blog_index_features_the_newest_post_first(): void
    {
        Post::factory()->create([
            'title' => 'Newest Story',
            'published_at' => now()->subDay(),
        ]);
        Post::factory()->create([
            'title' => 'Older Story',
            'published_at' => now()->subDays(5),
        ]);

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('Newest Story', false);
    }

    public function test_blog_index_filters_by_category(): void
    {
        $category = Category::factory()->create();
        Post::factory()->create(['category_id' => $category->id, 'title' => 'In Category']);
        Post::factory()->create(['title' => 'Outside Category']);

        $this->get(route('blog.index', ['category' => $category->slug]))
            ->assertOk()
            ->assertSee('In Category')
            ->assertDontSee('Outside Category');
    }

    public function test_blog_index_filters_by_tag(): void
    {
        $tag = Tag::factory()->create();
        $post = Post::factory()->create(['title' => 'Tagged Story']);
        $post->tags()->attach($tag);
        Post::factory()->create(['title' => 'Untagged Story']);

        $this->get(route('blog.index', ['tag' => $tag->slug]))
            ->assertOk()
            ->assertSee('Tagged Story')
            ->assertDontSee('Untagged Story');
    }

    public function test_blog_index_filters_by_author(): void
    {
        $author = User::factory()->author()->create(['username' => 'writer-one']);
        Post::factory()->create(['author_id' => $author->id, 'title' => 'Authored Story']);
        Post::factory()->create(['title' => 'Another Story']);

        $this->get(route('blog.index', ['author' => 'writer-one']))
            ->assertOk()
            ->assertSee('Authored Story')
            ->assertDontSee('Another Story');
    }

    public function test_blog_index_sorts_by_oldest(): void
    {
        $oldest = Post::factory()->create([
            'title' => 'Sort Oldest Story',
            'published_at' => now()->subDays(9),
        ]);
        $newest = Post::factory()->create([
            'title' => 'Sort Newest Story',
            'published_at' => now()->subDay(),
        ]);

        $this->get(route('blog.index', ['sort' => 'oldest']))
            ->assertOk()
            ->assertSeeInOrder([$oldest->title, $newest->title]);
    }

    public function test_blog_index_sorts_by_popularity(): void
    {
        $popular = Post::factory()->create(['title' => 'Very Popular Story', 'view_count' => 500]);
        $quiet = Post::factory()->create(['title' => 'Scarcely Read Story', 'view_count' => 5]);

        $this->get(route('blog.index', ['sort' => 'popular']))
            ->assertOk()
            ->assertSeeInOrder([$popular->title, $quiet->title]);
    }

    public function test_blog_index_paginates_posts(): void
    {
        Post::factory()->count(11)->create();

        $first = Post::orderByDesc('published_at')->skip(0)->first();
        $last = Post::orderByDesc('published_at')->skip(10)->first();

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee($first->title)
            ->assertDontSee($last->title);

        $this->get(route('blog.index', ['page' => 2]))
            ->assertOk()
            ->assertSee($last->title);
    }

    public function test_blog_index_shows_an_empty_state(): void
    {
        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee('No posts found');
    }

    public function test_blog_index_shows_an_empty_state_for_filters_without_matches(): void
    {
        Post::factory()->create(['title' => 'Existing Story']);

        $this->get(route('blog.index', ['author' => 'nobody']))
            ->assertOk()
            ->assertSee('No posts found')
            ->assertDontSee('Existing Story');
    }

    public function test_blog_index_preserves_filters_in_pagination_links(): void
    {
        Post::factory()->count(11)->create();

        $this->get(route('blog.index', ['sort' => 'oldest', 'page' => 2]))
            ->assertOk()
            ->assertSee('sort=oldest');
    }
}
