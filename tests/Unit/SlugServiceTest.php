<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\HtmlSanitizer;
use App\Services\SlugService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlugServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_a_slug_from_the_title(): void
    {
        $service = new SlugService;

        $this->assertSame('hello-world', $service->unique('Hello World', Category::class));
    }

    public function test_it_appends_a_numeric_suffix_for_duplicates(): void
    {
        $service = new SlugService;

        Category::factory()->create(['slug' => 'hello-world']);

        $this->assertSame('hello-world-2', $service->unique('Hello World', Category::class));

        Category::factory()->create(['slug' => 'hello-world-2']);

        $this->assertSame('hello-world-3', $service->unique('Hello World', Category::class));
    }

    public function test_it_ignores_the_given_model_when_checking_duplicates(): void
    {
        $service = new SlugService;
        $category = Category::factory()->create(['slug' => 'my-post']);

        $this->assertSame('my-post', $service->unique('My Post', Category::class, $category->id));
    }

    public function test_it_considers_soft_deleted_records_when_checking_duplicates(): void
    {
        $service = new SlugService;

        $post = Post::factory()->create(['slug' => 'taken-slug']);
        $post->delete();

        $this->assertSame('taken-slug-2', $service->unique('Taken Slug', Post::class));
    }

    public function test_it_uses_the_explicit_slug_when_provided(): void
    {
        $service = new SlugService;

        $this->assertSame('custom-path', $service->unique('Hello World', Tag::class, null, 'Custom Path'));
    }

    public function test_it_falls_back_when_the_slug_would_be_empty(): void
    {
        $service = new SlugService;

        $this->assertSame('item', $service->unique('!!!', Tag::class));
    }
}
