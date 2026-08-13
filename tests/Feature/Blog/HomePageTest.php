<?php

namespace Tests\Feature\Blog;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Stories worth reading, thoughtfully written.');
    }

    public function test_homepage_renders_the_latest_post_as_hero(): void
    {
        $post = Post::factory()->create(['title' => 'The Hero Story of the Week']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('The Hero Story of the Week')
            ->assertSee($post->author->name);
    }

    public function test_homepage_renders_featured_latest_and_trending_posts(): void
    {
        Post::factory()->count(8)->create();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Handpicked this week')
            ->assertSee('Fresh from the editors')
            ->assertSee('Most read right now');
    }

    public function test_homepage_renders_popular_categories(): void
    {
        $category = Category::factory()->create(['name' => 'Architecture']);
        Post::factory()->create(['category_id' => $category->id]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Browse by category')
            ->assertSee('Architecture');
    }

    public function test_homepage_renders_popular_authors(): void
    {
        $author = User::factory()->author()->create(['name' => 'Ines Lindqvist']);

        Post::factory()->create(['author_id' => $author->id]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('The people behind the words')
            ->assertSee('Ines Lindqvist');
    }

    public function test_homepage_does_not_render_draft_posts(): void
    {
        Post::factory()->draft()->create(['title' => 'Draft Must Not Be The Hero']);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Draft Must Not Be The Hero');
    }

    public function test_homepage_handles_an_empty_database(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('No stories yet');
    }

    public function test_homepage_includes_newsletter_placeholder(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('The Sunday Letter');
    }
}
