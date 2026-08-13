<x-admin-layout>
    <x-slot name="title">{{ __('Edit user') }}</x-slot>

    <div class="space-y-6">
        <div>
            <x-admin-breadcrumb :items="[
                ['label' => 'Users', 'url' => route('admin.users.index')],
                ['label' => $user->name, 'url' => route('admin.users.edit', $user)],
            ]" />
            <div class="mt-2 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">Edit user</h1>
                    <p class="mt-0.5 text-sm text-muted">Update {{ $user->name }}'s account details and access.</p>
                </div>
                <x-avatar :user="$user" size="md" />
            </div>
        </div>

        @if (session('status'))
            <x-alert type="success" :dismissible="true">{{ session('status') }}</x-alert>
        @endif

        @if (session('error'))
            <x-alert type="danger" :dismissible="true">{{ session('error') }}</x-alert>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="grid gap-6 lg:grid-cols-3">
            @csrf
            @method('PATCH')

            <div class="space-y-6 lg:col-span-2">
                <x-card>
                    <div class="space-y-4">
                        <div>
                            <h2 class="font-serif text-base font-semibold text-content">Account</h2>
                            <p class="mt-0.5 text-sm text-muted">The user's public profile and login details.</p>
                        </div>

                        <div>
                            <label for="name" class="label">Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" class="input-field" required autofocus>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="username" class="label">Username</label>
                                <input id="username" type="text" name="username" value="{{ old('username', $user->username) }}" class="input-field" placeholder="jane-doe">
                                <x-input-error :messages="$errors->get('username')" class="mt-2" />
                            </div>
                            <div>
                                <label for="email" class="label">Email</label>
                                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" class="input-field" required>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <div class="space-y-4">
                        <div>
                            <h2 class="font-serif text-base font-semibold text-content">Role & access</h2>
                            <p class="mt-0.5 text-sm text-muted">Controls what this user can do in the admin panel.</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="role_id" class="label">Role</label>
                                @if ($user->is(auth()->user()))
                                    <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                                    <div class="input-field flex items-center justify-between">
                                        <span>{{ $user->role?->name ?? 'User' }}</span>
                                        <x-badge variant="info">Your role</x-badge>
                                    </div>
                                @elseif ($user->isAdmin())
                                    <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                                    <div class="input-field flex items-center justify-between">
                                        <span>{{ $user->role?->name }}</span>
                                        <x-badge variant="danger">Protected</x-badge>
                                    </div>
                                    <p class="mt-1.5 text-xs text-muted">Admin roles cannot be changed by other admins.</p>
                                @else
                                    <x-select
                                        name="role_id"
                                        :options="$roles->pluck('name', 'id')->all()"
                                        :selected="$user->role_id"
                                    />
                                @endif
                                <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                            </div>

                            <div>
                                <label class="label">Account status</label>
                                @if ($user->is(auth()->user()))
                                    <div class="input-field flex items-center justify-between">
                                        <span class="text-content">Active</span>
                                        <x-badge variant="success">You</x-badge>
                                    </div>
                                @else
                                    <label class="flex cursor-pointer items-center justify-between rounded-xl border border-line bg-background px-4 py-2.5 transition-colors hover:border-line-strong">
                                        <span class="text-sm text-content-soft">Allow login &amp; publishing</span>
                                        <input type="checkbox" name="is_active" value="1" @checked($user->isActive()) class="h-4 w-4 rounded border-line-strong text-primary focus:ring-primary">
                                    </label>
                                @endif
                                <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card>
                    <h2 class="font-serif text-base font-semibold text-content">Summary</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-muted">Posts</dt>
                            <dd class="font-semibold text-content">{{ $user->posts()->count() }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-muted">Comments</dt>
                            <dd class="font-semibold text-content">{{ $user->comments()->count() }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-muted">Joined</dt>
                            <dd class="font-semibold text-content">{{ $user->created_at->format('M j, Y') }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-muted">Email verified</dt>
                            <dd class="font-semibold text-content">{{ $user->email_verified_at ? 'Yes' : 'No' }}</dd>
                        </div>
                    </dl>
                </x-card>

                <x-card class="border-danger/20">
                    <h2 class="font-serif text-base font-semibold text-danger">Danger zone</h2>
                    @if ($user->is(auth()->user()))
                        <p class="mt-2 text-sm text-muted">You cannot deactivate or delete your own account.</p>
                    @else
                        <div class="mt-4 space-y-2">
                            @if ($user->isActive())
                                <form method="POST" action="{{ route('admin.users.deactivate', $user) }}">
                                    @csrf
                                    <x-button variant="warning" size="md" type="submit" class="w-full">Deactivate account</x-button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                                    @csrf
                                    <x-button variant="success" size="md" type="submit" class="w-full">Activate account</x-button>
                                </form>
                            @endif
                            <x-button variant="danger" size="md" type="button" x-data x-on:click="$dispatch('open-modal', 'delete-user')" class="w-full">Delete user</x-button>
                        </div>
                    @endif
                </x-card>

                <div class="flex justify-end gap-3">
                    <x-button variant="ghost" size="md" href="{{ route('admin.users.index') }}">Cancel</x-button>
                    <x-button variant="primary" size="md" type="submit">Save changes</x-button>
                </div>
            </div>
        </form>
    </div>

    @if (! $user->is(auth()->user()))
        <x-modal name="delete-user" maxWidth="sm" focusable>
            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="p-6">
                @csrf
                @method('DELETE')
                <div class="flex items-start gap-4">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-danger-soft text-danger">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6" /><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /></svg>
                    </span>
                    <div>
                        <h2 class="font-serif text-lg font-semibold text-content">Delete {{ $user->name }}?</h2>
                        <p class="mt-1 text-sm text-muted">The account and all its posts and comments will be permanently removed.</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-button variant="ghost" size="md" type="button" x-data x-on:click="$dispatch('close')">Cancel</x-button>
                    <x-button variant="danger" size="md" type="submit">Delete user</x-button>
                </div>
            </form>
        </x-modal>
    @endif
</x-admin-layout>
