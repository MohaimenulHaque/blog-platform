<header class="sticky top-0 z-40 border-b border-line bg-background/80 backdrop-blur-md">
    <x-container class="flex h-16 items-center justify-between gap-4">
        <x-logo />

        <nav class="hidden items-center lg:flex" aria-label="Primary">
            @foreach (config('navigation.primary') as $item)
                <a
                    href="{{ route($item['route']) }}"
                    @class([
                        'nav-link link-underline',
                        'is-active' => request()->routeIs($item['route']),
                    ])
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-1">
            <a
                href="{{ route('search') }}"
                class="grid h-10 w-10 place-items-center rounded-xl text-muted transition-colors hover:bg-surface-alt hover:text-content"
                aria-label="Search"
                title="Search"
            >
                <x-icon icon="search" class="h-5 w-5" />
            </a>

            <div x-data="{ dark: document.documentElement.classList.contains('dark') }">
                <button
                    type="button"
                    x-on:click="dark = !dark; window.__theme.toggle()"
                    class="grid h-10 w-10 place-items-center rounded-xl text-muted transition-colors hover:bg-surface-alt hover:text-content"
                    :aria-label="dark ? 'Switch to light mode' : 'Switch to dark mode'"
                >
                    <x-icon icon="sun" x-show="dark" class="h-5 w-5" x-cloak />
                    <x-icon icon="moon" x-show="!dark" class="h-5 w-5" />
                </button>
            </div>

            @auth
                <a
                    href="{{ route('bookmarks.index') }}"
                    class="grid h-10 w-10 place-items-center rounded-xl text-muted transition-colors hover:bg-surface-alt hover:text-content"
                    aria-label="Bookmarks"
                    title="Bookmarks"
                >
                    <x-icon icon="bookmark" class="h-5 w-5" />
                </a>

                @php
                    $unreadCount = auth()->user()->unreadNotifications->count();
                @endphp
                <a
                    href="{{ route('notifications.index') }}"
                    class="relative grid h-10 w-10 place-items-center rounded-xl text-muted transition-colors hover:bg-surface-alt hover:text-content"
                    aria-label="Notifications"
                    title="Notifications"
                >
                    <x-icon icon="bell" class="h-5 w-5" />
                    @if ($unreadCount > 0)
                        <span class="absolute right-1 top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-danger px-1 text-[10px] font-bold leading-none text-white">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                <div x-data="{ open: false }" class="relative ml-1">
                    <button
                        type="button"
                        x-on:click="open = !open"
                        x-on:click.outside="open = false"
                        class="flex items-center gap-2 rounded-xl p-1.5 transition-colors hover:bg-surface-alt"
                    >
                        <x-avatar :user="auth()->user()" size="sm" />
                        <x-icon icon="chevron-down" class="hidden h-4 w-4 text-muted sm:block" x-bind:class="open ? 'rotate-180' : ''" />
                    </button>

                    <div
                        x-show="open"
                        x-transition
                        x-on:click.outside="open = false"
                        class="absolute right-0 z-50 mt-2 w-56 rounded-2xl border border-line bg-surface p-1.5 shadow-lift"
                        x-cloak
                    >
                        <div class="border-b border-line px-3 py-2.5">
                            <p class="truncate text-sm font-semibold text-content">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-muted">{{ auth()->user()->email }}</p>
                            <span class="mt-1.5 inline-flex items-center rounded-full bg-primary-soft px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-primary">
                                {{ auth()->user()->role?->name ?? 'User' }}
                            </span>
                        </div>
                        <div class="mt-1.5">
                            <a href="{{ route('dashboard') }}" class="dropdown-item">
                                <x-icon icon="sparkles" class="h-4 w-4" />
                                Dashboard
                            </a>
                            @can('author-content')
                                <a href="{{ route('admin.posts.index') }}" class="dropdown-item">
                                    <x-icon icon="pen" class="h-4 w-4" />
                                    Manage posts
                                </a>
                            @endcan
                            @can('access-admin')
                                <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                                    <x-icon icon="menu" class="h-4 w-4" />
                                    Admin area
                                </a>
                            @endcan
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                <x-icon icon="users" class="h-4 w-4" />
                                Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger hover:bg-danger-soft hover:text-danger">
                                    <x-icon icon="close" class="h-4 w-4" />
                                    Log out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost btn-md hidden sm:inline-flex">Log in</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-md ml-1 hidden sm:inline-flex">Get started</a>
            @endauth

            <button
                type="button"
                x-data
                x-on:click="$el.closest('header').querySelector('#mobile-menu').classList.toggle('hidden')"
                class="grid h-10 w-10 place-items-center rounded-xl text-muted transition-colors hover:bg-surface-alt hover:text-content lg:hidden"
                aria-label="Open menu"
                aria-controls="mobile-menu"
            >
                <x-icon icon="menu" class="h-5 w-5" />
            </button>
        </div>
    </x-container>

    <div id="mobile-menu" class="hidden border-t border-line bg-surface lg:hidden" x-cloak>
        <x-container class="space-y-1 py-4">
            @foreach (config('navigation.primary') as $item)
                <a
                    href="{{ route($item['route']) }}"
                    @class([
                        'block rounded-xl px-3 py-2.5 text-sm font-medium text-content-soft transition-colors hover:bg-surface-alt hover:text-content',
                        'bg-primary-soft text-primary hover:bg-primary-soft hover:text-primary' => request()->routeIs($item['route']),
                    ])
                >
                    {{ $item['label'] }}
                </a>
            @endforeach

            <div class="my-3 border-t border-line"></div>

            @auth
                <a href="{{ route('dashboard') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium text-content-soft transition-colors hover:bg-surface-alt hover:text-content">Dashboard</a>
                <a href="{{ route('bookmarks.index') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium text-content-soft transition-colors hover:bg-surface-alt hover:text-content">Bookmarks</a>
                <a href="{{ route('notifications.index') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium text-content-soft transition-colors hover:bg-surface-alt hover:text-content">Notifications</a>
                <a href="{{ route('profile.edit') }}" class="block rounded-xl px-3 py-2.5 text-sm font-medium text-content-soft transition-colors hover:bg-surface-alt hover:text-content">Profile</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-md w-full">Log out</button>
                </form>
            @else
                <div class="grid gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline btn-md w-full">Log in</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-md w-full">Get started</a>
                </div>
            @endauth
        </x-container>
    </div>
</header>
