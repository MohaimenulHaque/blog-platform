<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">
        <meta name="description" content="{{ $metaDescription ?? 'Administration area for ' . config('app.name') . '.' }}">

        <title>{{ isset($title) ? $title . ' · Admin · ' . config('app.name') : 'Admin · ' . config('app.name') }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <script>
            (function () {
                try {
                    var stored = localStorage.getItem('theme');
                    var dark = stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches);
                    document.documentElement.classList.toggle('dark', dark);
                } catch (e) {}
            })();
        </script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('head')
    </head>

    <body class="bg-background text-content font-sans antialiased">
        @php
            $toasts = collect()
                ->when(session('status'), fn ($c, $v) => $c->push(['type' => 'success', 'message' => $v]))
                ->when(session('success'), fn ($c, $v) => $c->push(['type' => 'success', 'message' => $v]))
                ->when(session('error'), fn ($c, $v) => $c->push(['type' => 'danger', 'message' => $v]))
                ->values();
        @endphp

        <div x-data="{ sidebarOpen: false }" class="flex min-h-screen">
            <aside
                class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col border-r border-line bg-surface transition-transform duration-300 lg:static lg:translate-x-0"
                :class="sidebarOpen ? 'translate-x-0' : ''"
                aria-label="Admin sidebar"
            >
                <div class="flex h-16 items-center border-b border-line px-6">
                    <x-logo />
                </div>

                <nav class="flex-1 space-y-1 overflow-y-auto p-4">
                    <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-muted">Main</p>

                    @can('access-admin')
                        <a href="{{ route('admin.dashboard') }}" class="dropdown-item {{ request()->routeIs('admin.dashboard') ? 'bg-primary-soft text-primary' : '' }}">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1" /><rect x="14" y="3" width="7" height="5" rx="1" /><rect x="14" y="12" width="7" height="9" rx="1" /><rect x="3" y="16" width="7" height="5" rx="1" /></svg>
                            Dashboard
                        </a>
                    @endcan

                    <p class="px-3 pb-2 pt-5 text-[10px] font-semibold uppercase tracking-[0.2em] text-muted">Content</p>

                    @can('author-content')
                        <a href="{{ route('admin.posts.index') }}" class="dropdown-item {{ request()->routeIs('admin.posts.*') ? 'bg-primary-soft text-primary' : '' }}">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9" /><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" /></svg>
                            Posts
                        </a>
                    @endcan

                    @can('manage-content')
                        <a href="{{ route('admin.categories.index') }}" class="dropdown-item {{ request()->routeIs('admin.categories.*') ? 'bg-primary-soft text-primary' : '' }}">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z" /><line x1="7" y1="7" x2="7.01" y2="7" /></svg>
                            Categories
                        </a>

                        <a href="{{ route('admin.tags.index') }}" class="dropdown-item {{ request()->routeIs('admin.tags.*') ? 'bg-primary-soft text-primary' : '' }}">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 2v20" /><path d="M20 20v-8" /><path d="M14 20v-14" /><path d="M8 20V8" /></svg>
                            Tags
                        </a>

                        <a href="{{ route('admin.comments.index') }}" class="dropdown-item {{ request()->routeIs('admin.comments.*') ? 'bg-primary-soft text-primary' : '' }}">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z" /></svg>
                            Comments
                            @php $pendingCount = \App\Models\Comment::pending()->count(); @endphp
                            @if ($pendingCount > 0)
                                <span class="ml-auto grid h-5 min-w-5 place-items-center rounded-full bg-danger-soft px-1.5 text-[10px] font-bold text-danger">{{ $pendingCount }}</span>
                            @endif
                        </a>

                        @can('manage-media')
                            <a href="{{ route('admin.media.index') }}" class="dropdown-item {{ request()->routeIs('admin.media.*') ? 'bg-primary-soft text-primary' : '' }}">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" /><circle cx="8.5" cy="8.5" r="1.5" /><path d="M21 15l-5-5L5 21" /></svg>
                                Media
                            </a>
                        @endcan
                    @endcan

                    <p class="px-3 pb-2 pt-5 text-[10px] font-semibold uppercase tracking-[0.2em] text-muted">People</p>

                    @can('manage-users')
                        <a href="{{ route('admin.users.index') }}" class="dropdown-item {{ request()->routeIs('admin.users.*') ? 'bg-primary-soft text-primary' : '' }}">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M23 21v-2a4 4 0 00-3-3.87" /><path d="M16 3.13a4 4 0 010 7.75" /></svg>
                            Users
                        </a>

                        <a href="{{ route('admin.authors.index') }}" class="dropdown-item {{ request()->routeIs('admin.authors.*') ? 'bg-primary-soft text-primary' : '' }}">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" /><circle cx="12" cy="7" r="4" /></svg>
                            Authors
                        </a>

                        <a href="{{ route('admin.newsletter.index') }}" class="dropdown-item {{ request()->routeIs('admin.newsletter.*') ? 'bg-primary-soft text-primary' : '' }}">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" /><polyline points="22,6 12,13 2,6" /></svg>
                            Newsletter
                        </a>
                    @endcan

                    @can('manage-settings')
                        <p class="px-3 pb-2 pt-5 text-[10px] font-semibold uppercase tracking-[0.2em] text-muted">System</p>

                        <a href="{{ route('admin.settings.index') }}" class="dropdown-item {{ request()->routeIs('admin.settings.*') ? 'bg-primary-soft text-primary' : '' }}">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z" /></svg>
                            Settings
                        </a>
                    @endcan

                    <p class="px-3 pb-2 pt-5 text-[10px] font-semibold uppercase tracking-[0.2em] text-muted">Site</p>

                    <a href="{{ route('blog.index') }}" class="dropdown-item" target="_blank" rel="noopener">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10" /><line x1="2" y1="12" x2="22" y2="12" /><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" /></svg>
                        View site
                    </a>
                </nav>

                <div class="border-t border-line p-4">
                    <div class="flex items-center gap-3 rounded-xl bg-surface-alt px-3 py-2.5">
                        <x-avatar size="sm" />
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-content">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-muted">{{ auth()->user()->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-muted transition-colors hover:text-danger" aria-label="Log out">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" /><polyline points="16 17 21 12 16 7" /><line x1="21" y1="12" x2="9" y2="12" /></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <div class="fixed inset-0 z-30 bg-black/40 lg:hidden" x-show="sidebarOpen" x-transition.opacity x-on:click="sidebarOpen = false"></div>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-20 border-b border-line bg-background/80 backdrop-blur-md">
                    <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6">
                        <div class="flex min-w-0 items-center gap-2">
                            <button type="button" class="grid h-10 w-10 shrink-0 place-items-center rounded-xl text-muted transition-colors hover:bg-surface-alt hover:text-content lg:hidden" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle sidebar">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6" /><line x1="4" y1="12" x2="20" y2="12" /><line x1="4" y1="18" x2="20" y2="18" /></svg>
                            </button>

                            <div class="hidden min-w-0 md:block">
                                {{ $breadcrumb ?? '' }}
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5">
                            @can('author-content')
                                <form method="GET" action="{{ route('admin.posts.index') }}" class="relative hidden sm:block" x-data>
                                    <svg class="pointer-events-none absolute inset-y-0 left-3 my-auto h-4 w-4 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8" /><line x1="21" y1="21" x2="16.65" y2="16.65" /></svg>
                                    <input
                                        type="text"
                                        name="search"
                                        placeholder="Search posts…"
                                        x-ref="globalSearch"
                                        @keydown.slash.window.prevent="$refs.globalSearch.focus()"
                                        class="input-field w-48 pl-9 py-2 transition-all focus:w-64"
                                    >
                                </form>
                            @endcan

                            <a href="{{ route('notifications.index') }}" class="relative grid h-10 w-10 place-items-center rounded-xl text-muted transition-colors hover:bg-surface-alt hover:text-content" aria-label="Notifications">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9" /><path d="M13.73 21a2 2 0 01-3.46 0" /></svg>
                                @if (auth()->user()->unreadNotifications()->count() > 0)
                                    <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-danger ring-2 ring-background"></span>
                                @endif
                            </a>

                            <div x-data="{ open: false }" class="relative" @keydown.escape.window="open = false">
                                <button
                                    type="button"
                                    class="flex items-center gap-2 rounded-xl p-1.5 transition-colors hover:bg-surface-alt"
                                    @click="open = ! open"
                                    aria-label="User menu"
                                    aria-haspopup="true"
                                    :aria-expanded="open"
                                >
                                    <x-avatar size="sm" />
                                    <span class="hidden max-w-40 truncate text-sm font-medium text-content md:block">{{ auth()->user()->name }}</span>
                                    <svg class="hidden h-4 w-4 text-muted md:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9" /></svg>
                                </button>

                                <div
                                    x-show="open"
                                    x-transition
                                    x-cloak
                                    @click.away="open = false"
                                    class="absolute right-0 mt-2 w-56 overflow-hidden rounded-2xl border border-line bg-surface p-1.5 shadow-lift"
                                >
                                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" /><circle cx="12" cy="7" r="4" /></svg>
                                        Profile
                                    </a>
                                    <a href="{{ route('notifications.index') }}" class="dropdown-item">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9" /><path d="M13.73 21a2 2 0 01-3.46 0" /></svg>
                                        Notifications
                                    </a>
                                    @can('manage-settings')
                                        <a href="{{ route('admin.settings.index') }}" class="dropdown-item">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z" /></svg>
                                            Settings
                                        </a>
                                    @endcan
                                    <a href="{{ route('blog.index') }}" target="_blank" rel="noopener" class="dropdown-item">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10" /><line x1="2" y1="12" x2="22" y2="12" /><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" /></svg>
                                        View site
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger hover:bg-danger-soft hover:text-danger">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4" /><polyline points="16 17 21 12 16 7" /><line x1="21" y1="12" x2="9" y2="12" /></svg>
                                            Log out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <div x-data="toasts(@js($toasts))" x-cloak class="pointer-events-none fixed right-4 top-4 z-[70] flex w-full max-w-sm flex-col items-end gap-2">
            <template x-for="(toast, i) in toasts" :key="i">
                <div
                    x-show="toast.show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="pointer-events-auto flex w-full items-start gap-3 rounded-xl border p-4 shadow-lift"
                    :class="toast.type === 'danger' ? 'border-danger/20 bg-danger-soft' : 'border-success/20 bg-success-soft'"
                >
                    <svg x-show="toast.type === 'danger'" class="mt-0.5 h-5 w-5 shrink-0 text-danger" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" /></svg>
                    <svg x-show="toast.type !== 'danger'" class="mt-0.5 h-5 w-5 shrink-0 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14" /><polyline points="22 4 12 14.01 9 11.01" /></svg>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold" :class="toast.type === 'danger' ? 'text-danger' : 'text-success'" x-text="toast.type === 'danger' ? 'Something went wrong' : 'Success'"></p>
                        <p class="mt-0.5 text-sm text-content-soft" x-text="toast.message"></p>
                    </div>
                    <button type="button" class="shrink-0 text-muted transition-colors hover:text-content" @click="toast.show = false" aria-label="Dismiss notification">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" /></svg>
                    </button>
                </div>
            </template>
        </div>
    </body>
</html>
