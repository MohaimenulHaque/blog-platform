<?php

namespace Tests\Feature\Blog;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminMediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admins_can_access_the_media_library(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.media.index'))
            ->assertOk()
            ->assertSee('Media library');
    }

    public function test_editors_cannot_access_the_media_library(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.media.index'))
            ->assertForbidden();
    }

    public function test_regular_users_cannot_access_the_media_library(): void
    {
        $user = User::factory()->withRole('user')->create();

        $this->actingAs($user)
            ->get(route('admin.media.index'))
            ->assertForbidden();
    }

    public function test_admins_can_upload_images(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.media.store'), [
                'files' => [
                    UploadedFile::fake()->image('hero.jpg', 400, 300),
                    UploadedFile::fake()->image('thumbnail.png', 200, 200),
                ],
            ])
            ->assertRedirect(route('admin.media.index'))
            ->assertSessionHas('status', '2 images uploaded.');

        $this->assertDatabaseCount('media', 2);

        $media = Media::first();
        $this->assertSame('hero', $media->name);
        $this->assertSame('hero.jpg', $media->original_name);
        $this->assertStringStartsWith('media/', $media->path);
        $this->assertSame($admin->id, $media->user_id);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_upload_requires_valid_images(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.media.store'), [
                'files' => [UploadedFile::fake()->create('notes.txt', 10)],
            ])
            ->assertSessionHasErrors('files.0');

        $this->assertDatabaseCount('media', 0);
    }

    public function test_media_library_supports_searching(): void
    {
        $admin = User::factory()->admin()->create();
        Media::factory()->create(['name' => 'Camping Photo']);
        Media::factory()->create(['name' => 'Recipe Card']);

        $this->actingAs($admin)
            ->get(route('admin.media.index', ['q' => 'camping']))
            ->assertOk()
            ->assertSee('Camping Photo')
            ->assertDontSee('Recipe Card');
    }

    public function test_admins_can_update_media_details(): void
    {
        $admin = User::factory()->admin()->create();
        $media = Media::factory()->create(['name' => 'Old Name']);

        $this->actingAs($admin)
            ->patch(route('admin.media.update', $media), [
                'name' => 'New Name',
                'alt_text' => 'A decorative alt description',
            ])
            ->assertRedirect()
            ->assertSessionHas('status', 'Media updated.');

        $this->assertDatabaseHas('media', [
            'id' => $media->id,
            'name' => 'New Name',
            'alt_text' => 'A decorative alt description',
        ]);
    }

    public function test_admins_can_delete_media(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $media = Media::factory()->create();
        Storage::disk('public')->put($media->path, 'binary');

        $this->actingAs($admin)
            ->delete(route('admin.media.destroy', $media))
            ->assertRedirect(route('admin.media.index'))
            ->assertSessionHas('status', 'Media deleted.');

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->path);
    }

    public function test_media_library_supports_picker_mode(): void
    {
        $admin = User::factory()->admin()->create();
        Media::factory()->create(['name' => 'Pick Me']);

        $this->actingAs($admin)
            ->get(route('admin.media.index', ['picker' => 1]))
            ->assertOk()
            ->assertSee('Use selected image')
            ->assertSee('Pick Me');
    }
}
