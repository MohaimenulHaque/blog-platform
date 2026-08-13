<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileAvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_an_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => UploadedFile::fake()->image('avatar.jpg', 100, 100),
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertNotNull($user->avatar);
        $this->assertNotNull($user->avatar_url);
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_old_avatar_is_deleted_when_replaced(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'avatar' => 'avatars/old-avatar.jpg',
        ]);

        Storage::disk('public')->put('avatars/old-avatar.jpg', 'old');

        $this->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => UploadedFile::fake()->image('new-avatar.jpg', 100, 100),
            ])
            ->assertSessionHasNoErrors();

        Storage::disk('public')->assertMissing('avatars/old-avatar.jpg');
        Storage::disk('public')->assertExists($user->fresh()->avatar);
    }

    public function test_invalid_avatar_is_rejected(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => UploadedFile::fake()->create('document.pdf', 100),
            ])
            ->assertSessionHasErrors('avatar');

        $this->assertNull($user->fresh()->avatar);
    }

    public function test_username_and_bio_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'username' => 'new-handle',
                'bio' => 'Hello, I write about tech.',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('new-handle', $user->username);
        $this->assertSame('Hello, I write about tech.', $user->bio);
        $this->assertSame('new-handle', $user->handle);
    }

    public function test_username_must_be_unique(): void
    {
        $other = User::factory()->create(['username' => 'taken-handle']);
        $user = User::factory()->create();

        $originalUsername = $user->username;

        $this->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'username' => 'taken-handle',
            ])
            ->assertSessionHasErrors('username');

        $this->assertSame($originalUsername, $user->fresh()->username);
        $this->assertSame('taken-handle', $other->fresh()->username);
    }
}
