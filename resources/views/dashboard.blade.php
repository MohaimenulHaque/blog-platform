<x-app-layout>
    <x-slot name="noindex">true</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Dashboard</p>
                <h2 class="mt-1 font-serif text-2xl font-semibold tracking-tight text-content">
                    {{ __('Welcome back, :name', ['name' => auth()->user()->name]) }}
                </h2>
            </div>
            <x-avatar :user="auth()->user()" size="md" />
        </div>
    </x-slot>

    <div class="py-12">
        <x-container>
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <x-card padded class="bg-gradient-to-br from-primary-soft via-surface to-secondary-soft">
                        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-4">
                                <x-avatar :user="auth()->user()" size="lg" class="ring-4 ring-surface" />
                                <div class="min-w-0">
                                    <p class="truncate font-serif text-lg font-semibold text-content">{{ auth()->user()->name }}</p>
                                    <p class="truncate text-sm text-muted">{{ auth()->user()->email }}</p>
                                    <div class="mt-1.5">
                                        <x-badge variant="primary">{{ auth()->user()->role?->name ?? 'User' }}</x-badge>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <x-button href="{{ route('blog.index') }}" variant="primary" size="sm">
                                    <x-icon icon="pen" class="h-4 w-4" />
                                    Read the blog
                                </x-button>
                                <x-button href="{{ route('profile.edit') }}" variant="outline" size="sm">
                                    <x-icon icon="users" class="h-4 w-4" />
                                    Manage profile
                                </x-button>
                            </div>
                        </div>

                        @if (auth()->user()->bio)
                            <p class="mt-5 max-w-2xl text-sm leading-relaxed text-muted">{{ auth()->user()->bio }}</p>
                        @else
                            <p class="mt-5 max-w-2xl text-sm leading-relaxed text-muted">
                                {{ __('Your account foundation is ready — role management, content tools and the full editorial experience arrive in the next phases.') }}
                            </p>
                        @endif
                    </x-card>

                    <x-card padded>
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">Recent activity</h3>
                            <x-badge variant="neutral">{{ __('Coming soon') }}</x-badge>
                        </div>
                        <div class="mt-4">
                            <x-empty-state
                                icon="inbox"
                                title="{{ __('No activity yet') }}"
                                description="{{ __('Your likes, comments and posts will show up here as you explore the platform.') }}"
                            />
                        </div>
                    </x-card>
                </div>

                <div class="space-y-6">
                    <x-card padded>
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">Bookmarks</h3>
                            <x-badge variant="neutral">{{ __('Coming soon') }}</x-badge>
                        </div>
                        <div class="mt-4">
                            <x-empty-state
                                icon="documents"
                                title="{{ __('No bookmarks yet') }}"
                                description="{{ __('Save posts you want to revisit and they will appear here.') }}"
                            />
                        </div>
                    </x-card>

                    <x-card padded>
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">Notifications</h3>
                            <x-badge variant="neutral">{{ __('Coming soon') }}</x-badge>
                        </div>
                        <div class="mt-4">
                            <x-empty-state
                                icon="bell"
                                title="{{ __('No notifications') }}"
                                description="{{ __('Replies, mentions and updates will land here.') }}"
                            />
                        </div>
                    </x-card>

                    <x-card padded>
                        <h3 class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">Quick links</h3>
                        <div class="mt-4 flex flex-col gap-1">
                            @can('access-admin')
                                <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                                    <x-icon icon="menu" class="h-4 w-4" />
                                    Admin area
                                </a>
                            @endcan
                            <a href="{{ route('categories.index') }}" class="dropdown-item">
                                <x-icon icon="sparkles" class="h-4 w-4" />
                                Categories
                            </a>
                            <a href="{{ route('search') }}" class="dropdown-item">
                                <x-icon icon="search" class="h-4 w-4" />
                                Search
                            </a>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" class="mt-4 border-t border-line pt-4">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger hover:bg-danger-soft hover:text-danger">
                                <x-icon icon="close" class="h-4 w-4" />
                                Log out
                            </button>
                        </form>
                    </x-card>
                </div>
            </div>
        </x-container>
    </div>
</x-app-layout>
