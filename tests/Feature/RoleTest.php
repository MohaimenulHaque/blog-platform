<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_roles_are_defined(): void
    {
        $this->seed(RoleSeeder::class);

        $this->assertDatabaseHas('roles', ['slug' => 'admin']);
        $this->assertDatabaseHas('roles', ['slug' => 'editor']);
        $this->assertDatabaseHas('roles', ['slug' => 'author']);
        $this->assertDatabaseHas('roles', ['slug' => 'user']);
    }

    public function test_users_can_be_assigned_a_role(): void
    {
        $user = User::factory()->create();

        $user->assignRole('author');

        $this->assertTrue($user->fresh()->hasRole('author'));
        $this->assertTrue($user->fresh()->isAuthor());
        $this->assertSame('author', $user->fresh()->role?->slug);
        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => Role::where('slug', 'author')->value('id'),
            'primary' => true,
        ]);
    }

    public function test_has_any_role_matches_any_of_the_roles(): void
    {
        $user = User::factory()->withRole('editor')->create();

        $this->assertTrue($user->hasAnyRole(['user', 'editor']));
        $this->assertFalse($user->hasAnyRole(['user', 'author']));
    }

    public function test_primary_role_can_be_changed(): void
    {
        $user = User::factory()->withRole('user')->create();

        $user->setPrimaryRole('admin');

        $fresh = $user->fresh();

        $this->assertSame('admin', $fresh->role?->slug);
        $this->assertTrue($fresh->isAdmin());

        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => Role::where('slug', 'admin')->value('id'),
            'primary' => true,
        ]);

        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => Role::where('slug', 'user')->value('id'),
            'primary' => false,
        ]);
    }

    public function test_role_can_be_removed(): void
    {
        $user = User::factory()->withRole('author')->create();

        $user->removeRole('author');

        $fresh = $user->fresh();

        $this->assertFalse($fresh->hasRole('author'));
        $this->assertNull($fresh->role_id);
    }

    public function test_role_helpers_are_available(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();
        $author = User::factory()->author()->create();
        $user = User::factory()->withRole('user')->create();

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isEditor());
        $this->assertTrue($editor->isEditor());
        $this->assertTrue($author->isAuthor());
        $this->assertTrue($user->hasRole('user'));
        $this->assertSame('Admin', $admin->role_name);
        $this->assertSame('Admin', $admin->role?->name);
    }
}
