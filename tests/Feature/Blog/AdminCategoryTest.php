<?php

namespace Tests\Feature\Blog;

use App\Enums\CategoryStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_access_the_categories_index(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.categories.index'))
            ->assertOk();
    }

    public function test_author_cannot_access_the_categories_index(): void
    {
        $author = User::factory()->author()->create();

        $this->actingAs($author)
            ->get(route('admin.categories.index'))
            ->assertForbidden();
    }

    public function test_editor_can_create_a_category(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->post(route('admin.categories.store'), [
                'name' => 'Business',
                'description' => 'All about business.',
                'status' => CategoryStatus::Active->value,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('categories', [
            'name' => 'Business',
            'slug' => 'business',
            'status' => CategoryStatus::Active->value,
        ]);
    }

    public function test_category_slug_is_uniquified(): void
    {
        $editor = User::factory()->editor()->create();
        Category::factory()->create(['name' => 'Business', 'slug' => 'business']);

        $this->actingAs($editor)
            ->post(route('admin.categories.store'), [
                'name' => 'Business',
                'status' => CategoryStatus::Active->value,
            ])
            ->assertRedirect();

        $this->assertSame(2, Category::where('slug', 'like', 'business%')->count());
    }

    public function test_editor_can_update_a_category(): void
    {
        $editor = User::factory()->editor()->create();
        $category = Category::factory()->create(['name' => 'Old Name']);

        $this->actingAs($editor)
            ->patch(route('admin.categories.update', $category), [
                'name' => 'New Name',
                'status' => CategoryStatus::Active->value,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'New Name',
            'slug' => 'new-name',
        ]);
    }

    public function test_category_with_posts_cannot_be_deleted(): void
    {
        $editor = User::factory()->editor()->create();
        $category = Category::factory()->create();
        Post::factory()->create(['category_id' => $category->id]);

        $this->actingAs($editor)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_editor_can_delete_and_restore_a_category(): void
    {
        $editor = User::factory()->editor()->create();
        $category = Category::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect();

        $this->assertSoftDeleted('categories', ['id' => $category->id]);

        $this->actingAs($editor)
            ->patch(route('admin.categories.restore', $category))
            ->assertRedirect();

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_trashed_categories_appear_on_the_trash_tab(): void
    {
        $editor = User::factory()->editor()->create();
        $category = Category::factory()->create();
        $category->delete();

        $this->actingAs($editor)
            ->get(route('admin.categories.index', ['trashed' => 1]))
            ->assertOk()
            ->assertSee($category->name);
    }

    public function test_regular_user_cannot_manage_categories(): void
    {
        $user = User::factory()->withRole('user')->create();

        $this->actingAs($user)
            ->post(route('admin.categories.store'), [
                'name' => 'Nope',
                'status' => CategoryStatus::Active->value,
            ])
            ->assertForbidden();
    }
}
