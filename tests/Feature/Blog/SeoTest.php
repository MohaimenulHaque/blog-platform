<?php

namespace Tests\Feature\Blog;

use App\Models\Post;
use App\Models\User;
use App\Services\PostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_meta_tags_and_website_structured_data(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<link rel="canonical"', false)
            ->assertSee('<meta property="og:title"', false)
            ->assertSee('<meta name="twitter:card"', false)
            ->assertSee('<script type="application/ld+json">', false)
            ->assertSee('"@type":"WebSite"', false);
    }

    public function test_post_page_renders_blog_posting_and_breadcrumb_structured_data(): void
    {
        $post = Post::factory()->create(['title' => 'Structured Article']);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('<script type="application/ld+json">', false)
            ->assertSee('"@type":"BlogPosting"', false)
            ->assertSee('"@type":"BreadcrumbList"', false)
            ->assertSee('<link rel="canonical"', false)
            ->assertSee($post->title);
    }

    public function test_index_pages_include_meta_descriptions(): void
    {
        $this->get(route('categories.index'))
            ->assertOk()
            ->assertSee('Browse all article categories', false);

        $this->get(route('tags.index'))
            ->assertOk()
            ->assertSee('Explore every topic tag', false);

        $this->get(route('authors.index'))
            ->assertOk()
            ->assertSee('Meet the writers behind the blog', false);
    }

    public function test_private_pages_are_noindexed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false);

        $this->actingAs($user)
            ->get(route('bookmarks.index'))
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_public_pages_are_indexable(): void
    {
        $post = Post::factory()->create(['title' => 'Indexable Article']);

        $this->get(route('home'))->assertDontSee('noindex, nofollow');
        $this->get(route('blog.show', $post->slug))->assertDontSee('noindex, nofollow');
    }

    public function test_robots_txt_disallows_private_areas(): void
    {
        $this->get(route('robots'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=utf-8')
            ->assertSee('User-agent: *')
            ->assertSee('Disallow: /admin/')
            ->assertSee('Disallow: /dashboard')
            ->assertSee('Disallow: /login')
            ->assertSee('Sitemap: '.url('/sitemap.xml'));
    }

    public function test_sitemap_includes_all_public_content(): void
    {
        $post = Post::factory()->create(['title' => 'Sitemap Story']);
        $category = $post->category;
        $tag = $post->tags->first();
        $author = $post->author;

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=utf-8')
            ->assertSee('<loc>'.route('home').'</loc>', false)
            ->assertSee(route('blog.show', $post->slug), false)
            ->assertSee(route('categories.show', $category->slug), false)
            ->assertSee(route('tags.show', $tag->slug), false)
            ->assertSee(route('authors.show', $author->username), false)
            ->assertDontSee('/admin');
    }

    public function test_sitemap_excludes_draft_and_private_posts(): void
    {
        Post::factory()->draft()->create(['title' => 'Draft Post Here']);
        Post::factory()->private()->create(['title' => 'Private Post Here']);

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertDontSee('draft-post-here')
            ->assertDontSee('private-post-here');
    }

    public function test_publishing_a_post_refreshes_the_cached_sitemap(): void
    {
        $post = Post::factory()->draft()->create([
            'title' => 'To Be Published',
            'slug' => 'to-be-published',
        ]);

        $this->get(route('sitemap'))->assertOk()->assertDontSee('to-be-published');

        app(PostService::class)->publish($post);

        $this->get(route('sitemap'))->assertOk()->assertSee('to-be-published');
    }
}
