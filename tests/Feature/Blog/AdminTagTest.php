<?php

namespace Tests\Feature\Blog;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTagTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_access_the_tags_index(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.tags.index'))
            ->assertOk();
    }

    public function test_author_cannot_access_the_tags_index(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->get(route('admin.tags.index'))
            ->assertForbidden();
    }

    public function test_editor_can_create_a_tag(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->post(route('admin.tags.store'), ['name' => 'Laravel'])
            ->assertRedirect();

        $this->assertDatabaseHas('tags', [
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);
    }

    public function test_duplicate_tag_slugs_are_uniquified(): void
    {
        $editor = User::factory()->editor()->create();
        Tag::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);

        $this->actingAs($editor)
            ->post(route('admin.tags.store'), ['name' => 'Laravel'])
            ->assertRedirect();

        $this->assertSame(2, Tag::where('slug', 'like', 'laravel%')->count());
    }

    public function test_editor_can_update_a_tag(): void
    {
        $editor = User::factory()->editor()->create();
        $tag = Tag::factory()->create(['name' => 'Old']);

        $this->actingAs($editor)
            ->patch(route('admin.tags.update', $tag), ['name' => 'New'])
            ->assertRedirect();

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'New',
            'slug' => 'new',
        ]);
    }

    public function test_editor_can_delete_and_restore_a_tag(): void
    {
        $editor = User::factory()->editor()->create();
        $tag = Tag::factory()->create(['name' => 'Laravel']);

        $this->actingAs($editor)
            ->delete(route('admin.tags.destroy', $tag))
            ->assertRedirect();

        $this->assertSoftDeleted('tags', ['id' => $tag->id]);

        $this->actingAs($editor)
            ->patch(route('admin.tags.restore', $tag))
            ->assertRedirect();

        $this->assertDatabaseHas('tags', ['id' => $tag->id]);
    }

    public function test_trashed_tags_appear_on_the_trash_tab(): void
    {
        $editor = User::factory()->editor()->create();
        $tag = Tag::factory()->create(['name' => 'Legacy']);
        $tag->delete();

        $this->actingAs($editor)
            ->get(route('admin.tags.index', ['trashed' => 1]))
            ->assertOk()
            ->assertSee('Legacy');
    }
}
