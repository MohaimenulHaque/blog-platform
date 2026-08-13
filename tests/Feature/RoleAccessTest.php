<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsAuthor;
use App\Http\Middleware\EnsureUserIsEditor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_the_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_regular_user_cannot_access_the_admin_dashboard(): void
    {
        $user = User::factory()->withRole('user')->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_editor_cannot_access_the_admin_dashboard(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)->get('/admin')->assertForbidden();
    }

    public function test_guest_is_redirected_from_the_admin_dashboard(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_all_authenticated_users_can_access_the_dashboard(): void
    {
        $user = User::factory()->withRole('user')->create();
        $editor = User::factory()->editor()->create();

        $this->actingAs($user)->get('/dashboard')->assertOk();
        $this->actingAs($editor)->get('/dashboard')->assertOk();
    }

    public function test_guest_is_redirected_from_the_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_admin_middleware_allows_admins_only(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->withRole('user')->create();

        $this->assertMiddlewarePasses(new EnsureUserIsAdmin, $admin);
        $this->assertMiddlewareForbids(new EnsureUserIsAdmin, $user);
    }

    public function test_editor_middleware_allows_editors_and_admins(): void
    {
        $editor = User::factory()->editor()->create();
        $admin = User::factory()->admin()->create();
        $user = User::factory()->withRole('user')->create();

        $this->assertMiddlewarePasses(new EnsureUserIsEditor, $editor);
        $this->assertMiddlewarePasses(new EnsureUserIsEditor, $admin);
        $this->assertMiddlewareForbids(new EnsureUserIsEditor, $user);
    }

    public function test_author_middleware_allows_authors_editors_and_admins(): void
    {
        $author = User::factory()->author()->create();
        $editor = User::factory()->editor()->create();
        $user = User::factory()->withRole('user')->create();

        $this->assertMiddlewarePasses(new EnsureUserIsAuthor, $author);
        $this->assertMiddlewarePasses(new EnsureUserIsAuthor, $editor);
        $this->assertMiddlewareForbids(new EnsureUserIsAuthor, $user);
    }

    public function test_generic_role_middleware_accepts_multiple_roles(): void
    {
        $author = User::factory()->author()->create();
        $user = User::factory()->withRole('user')->create();

        $this->assertMiddlewarePasses(new EnsureUserHasRole, $author, ['author', 'editor']);
        $this->assertMiddlewareForbids(new EnsureUserHasRole, $user, ['author', 'editor']);
    }

    public function test_authorization_gates_follow_role_hierarchy(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();
        $author = User::factory()->author()->create();
        $user = User::factory()->withRole('user')->create();

        $this->assertTrue($admin->can('access-admin'));
        $this->assertFalse($editor->can('access-admin'));

        $this->assertTrue($admin->can('manage-content'));
        $this->assertTrue($editor->can('manage-content'));
        $this->assertFalse($author->can('manage-content'));

        $this->assertTrue($admin->can('author-content'));
        $this->assertTrue($editor->can('author-content'));
        $this->assertTrue($author->can('author-content'));
        $this->assertFalse($user->can('author-content'));
    }

    private function assertMiddlewarePasses($middleware, User $user, array $roles = []): void
    {
        $request = Request::create('/', 'GET');
        $request->setUserResolver(fn () => $user);

        $response = $roles
            ? $middleware->handle($request, fn () => response('next'), ...$roles)
            : $middleware->handle($request, fn () => response('next'));

        $this->assertSame('next', $response->getContent());
    }

    private function assertMiddlewareForbids($middleware, User $user, array $roles = []): void
    {
        $request = Request::create('/', 'GET');
        $request->setUserResolver(fn () => $user);

        $this->assertThrows(
            fn () => $roles
                ? $middleware->handle($request, fn () => response('next'), ...$roles)
                : $middleware->handle($request, fn () => response('next')),
            HttpException::class
        );
    }
}
