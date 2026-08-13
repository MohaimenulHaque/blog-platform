<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): View
    {
        $this->authorize('manage-users');

        $query = User::query()
            ->with(['role'])
            ->withCount(['posts', 'comments'])
            ->search($request->query('search'));

        if ($request->filled('role')) {
            $query->whereHas('roles', fn (Builder $q) => $q->where('slug', $request->query('role')));
        }

        if ($request->filled('status')) {
            if ($request->query('status') === 'active') {
                $query->active();
            } elseif ($request->query('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $users = $query->latest()->paginate(config('blog.pagination.admin_users', 15))->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => Role::orderBy('name')->get(),
            'filters' => [
                'search' => $request->query('search'),
                'role' => $request->query('role'),
                'status' => $request->query('status'),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $this->authorize('manage-users');

        return view('admin.users.edit', [
            'user' => $user->load(['role', 'roles']),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('manage-users');

        $data = $request->validated();

        if ($user->is($request->user()) && isset($data['role_id']) && (int) $data['role_id'] !== $user->role_id) {
            return Redirect::route('admin.users.edit', $user)
                ->with('error', 'You cannot change your own role.');
        }

        if (isset($data['is_active']) && $user->is($request->user())) {
            unset($data['is_active']);
        }

        if (array_key_exists('is_active', $data) && ! $data['is_active'] && $this->isLastAdmin($user)) {
            return Redirect::route('admin.users.edit', $user)
                ->with('error', 'You cannot deactivate the last remaining admin.');
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'] ?? $user->username,
            'is_active' => $data['is_active'] ?? $user->is_active,
        ]);

        if (isset($data['role_id'])) {
            $this->changeRole($user, (int) $data['role_id'], $request->user());
        }

        return Redirect::route('admin.users.edit', $user)
            ->with('status', 'User updated.');
    }

    /**
     * Activate the specified user.
     */
    public function activate(Request $request, User $user): RedirectResponse
    {
        $this->authorize('manage-users');

        $user->update(['is_active' => true]);

        return back()->with('status', 'User activated.');
    }

    /**
     * Deactivate the specified user.
     */
    public function deactivate(Request $request, User $user): RedirectResponse
    {
        $this->authorize('manage-users');

        if ($user->is($request->user())) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        if ($this->isLastAdmin($user)) {
            return back()->with('error', 'You cannot deactivate the last remaining admin.');
        }

        $user->update(['is_active' => false]);

        return back()->with('status', 'User deactivated.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorize('manage-users');

        if ($user->is($request->user())) {
            return Redirect::route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        if ($this->isLastAdmin($user)) {
            return Redirect::route('admin.users.index')
                ->with('error', 'You cannot delete the last remaining admin.');
        }

        $user->delete();

        return Redirect::route('admin.users.index')
            ->with('status', 'User deleted.');
    }

    /**
     * Change the user's primary role, guarding against privilege escalation.
     */
    protected function changeRole(User $user, int $roleId, User $admin): void
    {
        $role = Role::find($roleId);

        if (! $role) {
            return;
        }

        // Admins may never have their admin role removed by another admin, and
        // the current admin may never change their own role.
        if ($user->isAdmin() && $role->slug !== 'admin') {
            return;
        }

        DB::transaction(function () use ($user, $role): void {
            $user->roles()->syncWithoutDetaching([$role->id => ['primary' => true]]);

            $user->roles()
                ->newPivotStatement()
                ->where('user_id', $user->id)
                ->where('role_id', '!=', $role->id)
                ->update(['primary' => false]);

            $user->forceFill(['role_id' => $role->id])->save();
        });
    }

    /**
     * Determine whether the user is the only remaining admin.
     */
    protected function isLastAdmin(User $user): bool
    {
        if (! $user->isAdmin()) {
            return false;
        }

        return User::query()
            ->whereHas('roles', fn (Builder $q) => $q->where('slug', 'admin'))
            ->count() <= 1;
    }
}
