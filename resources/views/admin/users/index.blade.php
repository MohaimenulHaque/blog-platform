<x-admin-layout>
    <x-slot name="title">{{ __('Users') }}</x-slot>

    <div class="space-y-6">
        <div>
            <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">Users</h1>
            <p class="mt-0.5 text-sm text-muted">Manage accounts, roles and access across the blog.</p>
        </div>

        @if (session('status'))
            <x-alert type="success" :dismissible="true">{{ session('status') }}</x-alert>
        @endif

        @if (session('error'))
            <x-alert type="danger" :dismissible="true">{{ session('error') }}</x-alert>
        @endif

        <x-card>
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col gap-3 border-b border-line p-4 lg:flex-row lg:items-center">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-muted">
                        <x-icon icon="search" class="h-4 w-4" />
                    </span>
                    <input
                        type="text"
                        name="search"
                        value="{{ $filters['search'] }}"
                        placeholder="Search users…"
                        class="input-field pl-10"
                    >
                </div>

                <div class="flex flex-col gap-2 sm:flex-row">
                    <div class="w-full sm:w-44">
                        <x-select
                            name="role"
                            :options="$roles->pluck('name', 'slug')->all()"
                            :selected="$filters['role']"
                            emptyOption="All roles"
                        />
                    </div>

                    <div class="w-full sm:w-44">
                        <x-select
                            name="status"
                            :options="['active' => 'Active', 'inactive' => 'Inactive']"
                            :selected="$filters['status']"
                            emptyOption="All statuses"
                        />
                    </div>

                    <x-button variant="primary" size="md" type="submit">Filter</x-button>

                    @if ($filters['search'] || $filters['role'] || $filters['status'])
                        <x-button variant="ghost" size="md" href="{{ route('admin.users.index') }}">Reset</x-button>
                    @endif
                </div>
            </form>

            @if ($users->isEmpty())
                <x-empty-state
                    icon="users"
                    title="No users found"
                    description="No users match your search or filters."
                />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-line text-xs uppercase tracking-wider text-muted">
                                <th class="px-4 py-3 font-semibold">User</th>
                                <th class="hidden px-4 py-3 font-semibold md:table-cell">Role</th>
                                <th class="hidden px-4 py-3 font-semibold lg:table-cell">Activity</th>
                                <th class="hidden px-4 py-3 font-semibold lg:table-cell">Joined</th>
                                <th class="hidden px-4 py-3 font-semibold sm:table-cell">Status</th>
                                <th class="px-4 py-3 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach ($users as $user)
                                <tr class="transition-colors hover:bg-surface-alt/50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <x-avatar :user="$user" size="sm" />
                                            <div>
                                                <p class="font-semibold text-content">{{ $user->name }}</p>
                                                <p class="text-xs text-muted">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden px-4 py-3 md:table-cell">
                                        <x-badge variant="{{ $user->isAdmin() ? 'danger' : ($user->isEditor() ? 'info' : 'neutral') }}">
                                            {{ $user->role?->name ?? 'User' }}
                                        </x-badge>
                                    </td>
                                    <td class="hidden px-4 py-3 text-content-soft lg:table-cell">
                                        {{ $user->posts_count }} posts · {{ $user->comments_count }} comments
                                    </td>
                                    <td class="hidden px-4 py-3 text-content-soft lg:table-cell">
                                        {{ $user->created_at->format('M j, Y') }}
                                    </td>
                                    <td class="hidden px-4 py-3 sm:table-cell">
                                        <x-badge variant="{{ $user->isActive() ? 'success' : 'warning' }}">
                                            {{ $user->isActive() ? 'Active' : 'Inactive' }}
                                        </x-badge>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="grid h-8 w-8 place-items-center rounded-lg text-muted transition-colors hover:bg-surface-alt hover:text-content" title="Edit" aria-label="Edit {{ $user->name }}">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z" /></svg>
                                            </a>

                                            @if (! $user->is(auth()->user()))
                                                @if ($user->isActive())
                                                    <form method="POST" action="{{ route('admin.users.deactivate', $user) }}">
                                                        @csrf
                                                        <button type="submit" class="grid h-8 w-8 place-items-center rounded-lg text-muted transition-colors hover:bg-warning-soft hover:text-warning" title="Deactivate" aria-label="Deactivate {{ $user->name }}">
                                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" /></svg>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                                                        @csrf
                                                        <button type="submit" class="grid h-8 w-8 place-items-center rounded-lg text-muted transition-colors hover:bg-success-soft hover:text-success" title="Activate" aria-label="Activate {{ $user->name }}">
                                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 4v6h-6" /><path d="M1 20v-6h6" /><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15" /></svg>
                                                        </button>
                                                    </form>
                                                @endif

                                                <button type="button" class="grid h-8 w-8 place-items-center rounded-lg text-muted transition-colors hover:bg-danger-soft hover:text-danger" x-data x-on:click="$dispatch('open-modal', 'remove-user-{{ $user->id }}')" title="Delete" aria-label="Delete {{ $user->name }}">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6" /><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /></svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-line p-4">
                    <x-pagination :paginator="$users" />
                </div>
            @endif
        </x-card>
    </div>

    @foreach ($users as $user)
        @if (! $user->is(auth()->user()))
            <x-modal name="remove-user-{{ $user->id }}" maxWidth="sm" focusable>
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="p-6">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-start gap-4">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-danger-soft text-danger">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 00-3-3.87" /><path d="M16 3.13a4 4 0 010 7.75" /></svg>
                        </span>
                        <div>
                            <h2 class="font-serif text-lg font-semibold text-content">Delete user?</h2>
                            <p class="mt-1 text-sm text-muted">
                                {{ $user->name }} will be permanently removed, along with their posts and comments. This cannot be undone.
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <x-button variant="ghost" size="md" type="button" x-data x-on:click="$dispatch('close')">Cancel</x-button>
                        <x-button variant="danger" size="md" type="submit">Delete user</x-button>
                    </div>
                </form>
            </x-modal>
        @endif
    @endforeach
</x-admin-layout>
