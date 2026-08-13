<?php

namespace Tests\Feature\Blog;

use App\Enums\PostVisibility;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_show_renders_breadcrumb(): void
    {
        $post = Post::factory()->create(['title' => 'Breadcrumb Story']);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('Breadcrumb')
            ->assertSee('Breadcrumb Story');
    }

    public function test_blog_show_renders_meta_and_meta_description(): void
    {
        $post = Post::factory()->create([
            'title' => 'Seo Story',
            'meta_description' => 'A hand-crafted description.',
        ]);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('A hand-crafted description.')
            ->assertSee('canonical');
    }

    public function test_blog_show_renders_excerpt_author_date_and_read_time(): void
    {
        $post = Post::factory()->create([
            'title' => 'Metadata Story',
            'excerpt' => 'A short teaser for the readers.',
            'reading_time' => 7,
        ]);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('A short teaser for the readers.')
            ->assertSee($post->author->name)
            ->assertSee('7 min read');
    }

    public function test_blog_show_renders_a_table_of_contents(): void
    {
        $post = Post::factory()->create([
            'title' => 'Toc Story',
            'content' => '<h2>First Section</h2><p>Body text.</p><h3>Sub Heading</h3><p>More text.</p>',
        ]);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('On this page')
            ->assertSee('First Section')
            ->assertSee('Sub Heading')
            ->assertSee('href="#heading-1"', false)
            ->assertSee('href="#heading-2"', false);
    }

    public function test_blog_show_renders_article_headings_with_anchor_ids(): void
    {
        $post = Post::factory()->create([
            'title' => 'Anchor Story',
            'content' => '<h2>Anchored Heading</h2><p>Content.</p>',
        ]);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('id="heading-1"', false);
    }

    public function test_blog_show_renders_tags(): void
    {
        $post = Post::factory()->create(['title' => 'Tagged Story']);
        $tag = Tag::factory()->create(['name' => 'Laravel']);
        $post->tags()->sync([$tag->id]);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('#Laravel');
    }

    public function test_blog_show_renders_related_posts_from_the_same_category(): void
    {
        $category = Category::factory()->create();
        Post::factory()->create(['category_id' => $category->id, 'title' => 'Related Story']);
        $post = Post::factory()->create(['category_id' => $category->id, 'title' => 'Current Story']);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('More to explore')
            ->assertSee('Related Story');
    }

    public function test_blog_show_renders_previous_and_next_article(): void
    {
        $older = Post::factory()->create(['title' => 'Older Article', 'published_at' => now()->subDays(4)]);
        $current = Post::factory()->create(['title' => 'Current Article', 'published_at' => now()->subDays(2)]);
        $newer = Post::factory()->create(['title' => 'Newer Article', 'published_at' => now()->subDay()]);

        $this->get(route('blog.show', $current->slug))
            ->assertOk()
            ->assertSee('Previous post')
            ->assertSee('Older Article')
            ->assertSee('Next post')
            ->assertSee('Newer Article');

        $this->get(route('blog.show', $older->slug))
            ->assertOk()
            ->assertDontSee('Previous post');
    }

    public function test_blog_show_renders_an_author_box(): void
    {
        $post = Post::factory()->create(['title' => 'Authored Story']);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('About the author')
            ->assertSee($post->author->name);
    }

    public function test_blog_show_returns_404_for_an_invalid_slug(): void
    {
        $this->get(route('blog.show', 'this-slug-does-not-exist'))->assertNotFound();
    }

    public function test_blog_show_does_not_expose_private_posts(): void
    {
        $post = Post::factory()->create([
            'title' => 'Secret Story',
            'visibility' => PostVisibility::Private->value,
        ]);

        $this->get(route('blog.show', $post->slug))->assertNotFound();
    }

    public function test_blog_show_renders_the_updated_date_when_modified(): void
    {
        $post = Post::factory()->create(['title' => 'Revised Story']);

        $post->update(['updated_at' => now()->addDay()]);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('Updated ');
    }
}
