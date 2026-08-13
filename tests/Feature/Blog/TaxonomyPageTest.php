<?php

namespace Tests\Feature\Blog;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxonomyPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_page_renders_category_name_and_count(): void
    {
        $category = Category::factory()->create(['name' => 'Photography']);
        Post::factory()->count(2)->create(['category_id' => $category->id]);

        $this->get(route('categories.show', $category->slug))
            ->assertOk()
            ->assertSee('Photography')
            ->assertSee('2 posts');
    }

    public function test_category_page_renders_related_categories(): void
    {
        $category = Category::factory()->create(['name' => 'Gardening']);
        Post::factory()->create(['category_id' => $category->id]);
        Category::factory()->create(['name' => 'More Gardening']);

        $this->get(route('categories.show', $category->slug))
            ->assertOk()
            ->assertSee('More Gardening');
    }

    public function test_category_page_shows_empty_state_when_no_posts(): void
    {
        $category = Category::factory()->create(['name' => 'Empty Category']);

        $this->get(route('categories.show', $category->slug))
            ->assertOk()
            ->assertSee('Nothing in Empty Category yet');
    }

    public function test_tag_page_renders_tag_and_posts(): void
    {
        $tag = Tag::factory()->create(['name' => 'Productivity']);
        $post = Post::factory()->create(['title' => 'Focus Deeply']);
        $post->tags()->attach($tag);

        $this->get(route('tags.show', $tag->slug))
            ->assertOk()
            ->assertSee('#Productivity')
            ->assertSee('Focus Deeply');
    }

    public function test_tag_page_shows_an_empty_state(): void
    {
        $tag = Tag::factory()->create(['name' => 'Lonely Tag']);

        $this->get(route('tags.show', $tag->slug))
            ->assertOk()
            ->assertSee('No posts tagged Lonely Tag yet');
    }

    public function test_author_page_renders_profile_and_stats(): void
    {
        $author = User::factory()->author()->create([
            'name' => 'Mira Kelani',
            'username' => 'mira-kelani',
            'bio' => 'Writes about cities and light.',
        ]);
        Post::factory()->count(3)->create(['author_id' => $author->id, 'title' => 'City Essays']);

        $this->get(route('authors.show', $author->username))
            ->assertOk()
            ->assertSee('Mira Kelani')
            ->assertSee('Writes about cities and light.')
            ->assertSee('3 published posts')
            ->assertSee('Posts by Mira Kelani')
            ->assertSee('Total reads');
    }

    public function test_author_page_shows_empty_state(): void
    {
        $author = User::factory()->author()->create(['name' => 'Quiet Author', 'username' => 'quiet']);

        $this->get(route('authors.show', $author->username))
            ->assertOk()
            ->assertSee("Quiet Author hasn't published anything yet");
    }

    public function test_unknown_slugs_return_404(): void
    {
        $this->get(route('categories.show', 'nope'))->assertNotFound();
        $this->get(route('tags.show', 'nope'))->assertNotFound();
        $this->get(route('authors.show', 'nope'))->assertNotFound();
    }

    public function test_inactive_category_page_returns_404(): void
    {
        $category = Category::factory()->inactive()->create();

        $this->get(route('categories.show', $category->slug))->assertNotFound();
    }
}
