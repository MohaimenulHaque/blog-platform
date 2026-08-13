<?php

namespace Tests\Feature\Blog;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admins_can_access_the_users_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Users');
    }

    public function test_editors_cannot_access_the_users_index(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_regular_users_cannot_access_the_users_index(): void
    {
        $user = User::factory()->withRole('user')->create();

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_users_index_supports_search_and_filters(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->withRole('user')->create(['name' => 'Unique Jane', 'email' => 'jane@example.com']);
        User::factory()->author()->create(['name' => 'Bob Author']);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['search' => 'Unique Jane']))
            ->assertOk()
            ->assertSee('Unique Jane')
            ->assertDontSee('Bob Author');

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['role' => 'author']))
            ->assertOk()
            ->assertSee('Bob Author')
            ->assertDontSee('Unique Jane');
    }

    public function test_admin_can_edit_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->withRole('user')->create();

        $this->actingAs($admin)
            ->get(route('admin.users.edit', $user))
            ->assertOk()
            ->assertSee($user->name);
    }

    public function test_admin_can_update_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->withRole('user')->create();
        $editorRole = \App\Models\Role::firstOrCreate(['slug' => 'editor'], ['name' => 'Editor']);

        $this->actingAs($admin)
            ->patch(route('admin.users.update', $user), [
                'name' => 'Updated Name',
                'username' => 'updated-name',
                'email' => 'updated@example.com',
                'role_id' => $editorRole->id,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.users.edit', $user))
            ->assertSessionHas('status', 'User updated.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'username' => 'updated-name',
            'email' => 'updated@example.com',
            'role_id' => $editorRole->id,
        ]);
    }

    public function test_admin_cannot_change_their_own_role(): void
    {
        $admin = User::factory()->admin()->create();
        $userRole = \App\Models\Role::firstOrCreate(['slug' => 'user'], ['name' => 'User']);

        $this->actingAs($admin)
            ->patch(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'role_id' => $userRole->id,
            ])
            ->assertSessionHas('error', 'You cannot change your own role.');

        $this->assertTrue($admin->fresh()->isAdmin());
    }

    public function test_admin_cannot_deactivate_their_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.deactivate', $admin))
            ->assertSessionHas('error', 'You cannot deactivate your own account.');

        $this->assertTrue($admin->fresh()->isActive());
    }

    public function test_admin_can_deactivate_another_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->withRole('user')->create();

        $this->actingAs($admin)
            ->post(route('admin.users.deactivate', $user))
            ->assertSessionHas('status', 'User deactivated.');

        $this->assertFalse($user->fresh()->isActive());
    }

    public function test_admin_can_reactivate_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->withRole('user')->create(['is_active' => false]);

        $this->actingAs($admin)
            ->post(route('admin.users.activate', $user))
            ->assertSessionHas('status', 'User activated.');

        $this->assertTrue($user->fresh()->isActive());
    }

    public function test_deactivated_users_are_blocked_from_logging_in(): void
    {
        $user = User::factory()->withRole('user')->create([
            'email' => 'locked@example.com',
            'password' => bcrypt('password'),
            'is_active' => false,
        ]);

        $this->post('/login', [
            'email' => 'locked@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_cannot_deactivate_the_last_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $other = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.deactivate', $admin))
            ->assertSessionHas('error', 'You cannot deactivate your own account.');

        $this->assertTrue($admin->fresh()->isActive());
        $this->assertTrue($other->fresh()->isActive());
    }

    public function test_admin_cannot_delete_the_last_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->admin()->create();
        $other = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertSessionHas('error', 'You cannot delete your own account.');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
        $this->assertDatabaseHas('users', ['id' => $other->id]);
    }

    public function test_admin_can_delete_another_user(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();
        $user = User::factory()->withRole('user')->create();

        $this->actingAs($otherAdmin)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status', 'User deleted.');

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
