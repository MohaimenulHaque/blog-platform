<?php

namespace Tests\Feature\Blog;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminAuthorTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_the_authors_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.authors.index'))
            ->assertOk();
    }

    public function test_editor_cannot_access_the_authors_index(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.authors.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_an_author(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.authors.store'), [
                'name' => 'Rosa Díaz',
                'username' => 'rosa',
                'email' => 'rosa@example.com',
                'password' => 'password',
                'designation' => 'Contributing Writer',
                'bio' => 'Rosa writes about cities and urban design.',
                'website' => 'https://rosadiaz.example.com',
                'social_links' => [
                    'twitter' => 'https://twitter.com/rosadiaz',
                ],
            ])
            ->assertRedirect();

        $user = User::where('email', 'rosa@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue($user->isAuthor());
        $this->assertSame('rosa', $user->username);
        $this->assertSame(['twitter' => 'https://twitter.com/rosadiaz'], $user->social_links);
    }

    public function test_admin_can_update_an_author(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->author()->create(['name' => 'Old Name']);

        $this->actingAs($admin)
            ->patch(route('admin.authors.update', $author), [
                'name' => 'New Name',
                'username' => 'newusername',
                'email' => $author->email,
                'designation' => 'Lead Writer',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $author->id,
            'name' => 'New Name',
            'designation' => 'Lead Writer',
        ]);
    }

    public function test_admin_can_update_the_author_avatar(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $author = User::factory()->author()->create();

        $this->actingAs($admin)
            ->patch(route('admin.authors.update', $author), [
                'name' => $author->name,
                'username' => 'avataruser',
                'email' => $author->email,
                'avatar' => UploadedFile::fake()->image('avatar.png', 300, 300),
            ])
            ->assertRedirect();

        Storage::disk('public')->assertExists($author->fresh()->avatar);

        $this->assertNotNull($author->fresh()->avatar);
    }

    public function test_admin_can_remove_the_author_role(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->author()->create();

        $this->actingAs($admin)
            ->delete(route('admin.authors.destroy', $author))
            ->assertRedirect();

        $this->assertFalse($author->fresh()->isAuthor());
        $this->assertDatabaseHas('users', ['id' => $author->id]);
    }

    public function test_authors_index_shows_post_counts(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->author()->create(['name' => 'Zara Fox']);

        $this->actingAs($admin)
            ->get(route('admin.authors.index'))
            ->assertOk()
            ->assertSee('Zara Fox');
    }
}
