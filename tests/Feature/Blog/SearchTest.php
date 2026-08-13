<?php

namespace Tests\Feature\Blog;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_page_renders(): void
    {
        $this->get(route('search'))
            ->assertOk()
            ->assertSee('Find a story');
    }

    public function test_search_with_no_query_shows_a_prompt_and_suggestions(): void
    {
        $category = Category::factory()->create(['name' => 'Travel']);
        $tag = Tag::factory()->create(['name' => 'Europe']);

        $this->get(route('search'))
            ->assertOk()
            ->assertSee('Type to search the archive')
            ->assertSee('Travel')
            ->assertSee('#Europe');
    }

    public function test_search_matches_post_titles(): void
    {
        Post::factory()->create(['title' => 'A Unique Needle Story']);
        Post::factory()->create(['title' => 'Unrelated Title']);

        $this->get(route('search', ['q' => 'Unique Needle']))
            ->assertOk()
            ->assertSee('A Unique Needle Story')
            ->assertDontSee('Unrelated Title');
    }

    public function test_search_matches_post_content(): void
    {
        Post::factory()->create([
            'title' => 'Content Match Story',
            'content' => '<p>This body contains a rare-fragment phrase.</p>',
        ]);

        $this->get(route('search', ['q' => 'rare-fragment']))
            ->assertOk()
            ->assertSee('Content Match Story');
    }

    public function test_search_matches_categories(): void
    {
        $category = Category::factory()->create(['name' => 'Astronomy']);
        Post::factory()->create(['category_id' => $category->id, 'title' => 'Stars of the Night']);

        $this->get(route('search', ['q' => 'Astronomy']))
            ->assertOk()
            ->assertSee('Stars of the Night');
    }

    public function test_search_matches_tags(): void
    {
        $tag = Tag::factory()->create(['name' => 'Wellbeing']);
        $post = Post::factory()->create(['title' => 'Calm Mornings']);
        $post->tags()->attach($tag);

        $this->get(route('search', ['q' => 'Wellbeing']))
            ->assertOk()
            ->assertSee('Calm Mornings');
    }

    public function test_search_matches_author_names(): void
    {
        $author = User::factory()->author()->create(['name' => 'Elias Nordstrom']);
        Post::factory()->create(['author_id' => $author->id, 'title' => 'Nordic Essays']);

        $this->get(route('search', ['q' => 'Elias Nordstrom']))
            ->assertOk()
            ->assertSee('Nordic Essays');
    }

    public function test_search_shows_an_empty_state_when_nothing_matches(): void
    {
        Post::factory()->create(['title' => 'Existing Story']);

        $this->get(route('search', ['q' => 'zzz-no-such-term']))
            ->assertOk()
            ->assertSee('No results found')
            ->assertDontSee('Existing Story');
    }

    public function test_search_paginates_results(): void
    {
        Post::factory()->count(11)->create(['title' => 'Common Searchable Term']);

        $this->get(route('search', ['q' => 'Common Searchable']))
            ->assertOk()
            ->assertSee('11</strong> results', false);

        $this->get(route('search', ['q' => 'Common Searchable', 'page' => 2]))
            ->assertOk()
            ->assertSee('11</strong> results', false);
    }

    public function test_search_preserves_the_query_in_pagination_links(): void
    {
        Post::factory()->count(11)->create(['title' => 'Another Shared Phrase']);

        $this->get(route('search', ['q' => 'Another Shared', 'page' => 2]))
            ->assertOk()
            ->assertSee('q=Another', false);
    }

    public function test_search_only_finds_published_posts(): void
    {
        Post::factory()->create(['title' => 'Visible Search Result']);
        Post::factory()->draft()->create(['title' => 'Hidden Search Result']);

        $this->get(route('search', ['q' => 'Search Result']))
            ->assertOk()
            ->assertSee('Visible Search Result')
            ->assertDontSee('Hidden Search Result');
    }
}
