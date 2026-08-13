<x-admin-layout>
    <x-slot name="title">{{ __('Dashboard') }}</x-slot>

    <div class="space-y-6" x-data="dashboardCharts(@js($charts))">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">Welcome back, {{ auth()->user()->name }}</h1>
                <p class="mt-0.5 text-sm text-muted">Here's what's happening across the blog today.</p>
            </div>
            @can('author-content')
                <x-button variant="primary" size="md" href="{{ route('admin.posts.create') }}">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14" /><path d="M5 12h14" /></svg>
                    New post
                </x-button>
            @endcan
        </div>

        @if ($stats['pendingComments'] > 0)
            <a href="{{ route('admin.comments.index') }}" class="group flex items-center gap-3 rounded-2xl border border-warning/30 bg-warning-soft px-5 py-4 transition-colors hover:border-warning/50">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-warning/15 text-warning">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4" /><path d="M12 17h.01" /><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" /></svg>
                </span>
                <p class="flex-1 text-sm text-content">
                    <strong>{{ $stats['pendingComments'] }}</strong> comment{{ $stats['pendingComments'] === 1 ? '' : 's' }} awaiting moderation
                </p>
                <svg class="h-4 w-4 text-muted transition-transform group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12" /><polyline points="12 5 19 12 12 19" /></svg>
            </a>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-card class="relative overflow-hidden p-5">
                <span class="pointer-events-none absolute -right-4 -top-4 h-20 w-20 rounded-full bg-primary/10"></span>
                <div class="flex items-center gap-4">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-primary-soft text-primary">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9" /><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z" /></svg>
                    </span>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted">Published posts</p>
                        <p class="mt-0.5 font-serif text-3xl font-semibold text-content">{{ $stats['published'] }}</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between border-t border-line pt-3 text-xs text-muted">
                    <span>{{ $stats['draft'] }} draft · {{ $stats['scheduled'] }} scheduled</span>
                    <span class="text-content-soft">{{ $stats['posts'] }} total</span>
                </div>
            </x-card>

            <x-card class="relative overflow-hidden p-5">
                <span class="pointer-events-none absolute -right-4 -top-4 h-20 w-20 rounded-full bg-success/10"></span>
                <div class="flex items-center gap-4">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-success-soft text-success">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M23 21v-2a4 4 0 00-3-3.87" /><path d="M16 3.13a4 4 0 010 7.75" /></svg>
                    </span>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted">Users</p>
                        <p class="mt-0.5 font-serif text-3xl font-semibold text-content">{{ $stats['users'] }}</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between border-t border-line pt-3 text-xs text-muted">
                    <span>Active readers</span>
                    <a href="{{ route('admin.users.index') }}" class="font-medium text-primary hover:underline">Manage</a>
                </div>
            </x-card>

            <x-card class="relative overflow-hidden p-5">
                <span class="pointer-events-none absolute -right-4 -top-4 h-20 w-20 rounded-full bg-warning/10"></span>
                <div class="flex items-center gap-4">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-warning-soft text-warning">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z" /></svg>
                    </span>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted">Comments</p>
                        <p class="mt-0.5 font-serif text-3xl font-semibold text-content">{{ $stats['comments'] }}</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between border-t border-line pt-3 text-xs text-muted">
                    <span class="text-warning">{{ $stats['pendingComments'] }} pending</span>
                    <a href="{{ route('admin.comments.index') }}" class="font-medium text-primary hover:underline">Moderate</a>
                </div>
            </x-card>

            <x-card class="relative overflow-hidden p-5">
                <span class="pointer-events-none absolute -right-4 -top-4 h-20 w-20 rounded-full bg-secondary/10"></span>
                <div class="flex items-center gap-4">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-secondary-soft text-secondary">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10" /><line x1="2" y1="12" x2="22" y2="12" /><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" /></svg>
                    </span>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-muted">Total views</p>
                        <p class="mt-0.5 font-serif text-3xl font-semibold text-content">{{ number_format($stats['views']) }}</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between border-t border-line pt-3 text-xs text-muted">
                    <span>{{ number_format($stats['likes']) }} likes</span>
                    <span class="text-content-soft">{{ number_format($stats['subscribers']) }} subscribers</span>
                </div>
            </x-card>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <x-card class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-serif text-base font-semibold text-content">Publishing activity</h2>
                        <p class="mt-0.5 text-sm text-muted">Posts published in the last 12 months.</p>
                    </div>
                    <x-badge variant="primary">{{ $charts['labels'][count($charts['labels']) - 1] ?? now()->format('M Y') }}</x-badge>
                </div>
                <div class="mt-6 h-64">
                    <canvas id="chart-posts-published"></canvas>
                </div>
            </x-card>

            <x-card class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-serif text-base font-semibold text-content">Popular categories</h2>
                        <p class="mt-0.5 text-sm text-muted">Published posts per category.</p>
                    </div>
                    <x-badge variant="neutral">{{ count($charts['popularCategories']['labels']) }} categories</x-badge>
                </div>
                <div class="mt-6 h-64">
                    <canvas id="chart-categories"></canvas>
                </div>
            </x-card>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <x-card class="p-6 lg:col-span-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-serif text-base font-semibold text-content">New users & comments</h2>
                        <p class="mt-0.5 text-sm text-muted">Signups and engagement in the last 12 months.</p>
                    </div>
                </div>
                <div class="mt-6 h-56">
                    <canvas id="chart-users"></canvas>
                </div>
            </x-card>

            <x-card class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-serif text-base font-semibold text-content">Top posts</h2>
                        <p class="mt-0.5 text-sm text-muted">By total views.</p>
                    </div>
                </div>
                @if (count($charts['popularPosts']['labels']))
                    <div class="mt-4 h-64">
                        <canvas id="chart-top-posts"></canvas>
                    </div>
                @else
                    <x-empty-state icon="sparkles" title="No posts yet" description="Published posts will rank here by views." />
                @endif
            </x-card>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <x-card>
                <div class="flex items-center justify-between border-b border-line px-6 py-4">
                    <h2 class="font-serif text-base font-semibold text-content">Recent posts</h2>
                    <a href="{{ route('admin.posts.index') }}" class="text-sm font-medium text-primary hover:underline">View all</a>
                </div>
                @if ($recentPosts->isEmpty())
                    <div class="p-6">
                        <x-empty-state icon="sparkles" title="No posts yet" description="Create your first post to see it here." />
                    </div>
                @else
                    <ul class="divide-y divide-line">
                        @foreach ($recentPosts as $post)
                            <li class="flex items-center gap-3 px-6 py-3.5 transition-colors hover:bg-surface-alt/50">
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="truncate text-sm font-semibold text-content hover:text-primary">{{ $post->title }}</a>
                                    <p class="mt-0.5 text-xs text-muted">
                                        {{ $post->author?->name }} · {{ $post->published_at?->diffForHumans() ?? $post->updated_at->diffForHumans() }}
                                    </p>
                                </div>
                                <x-badge variant="{{ $post->status_badge }}">{{ $post->status_label }}</x-badge>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-card>

            <x-card>
                <div class="flex items-center justify-between border-b border-line px-6 py-4">
                    <h2 class="font-serif text-base font-semibold text-content">Recent comments</h2>
                    <a href="{{ route('admin.comments.index') }}" class="text-sm font-medium text-primary hover:underline">View all</a>
                </div>
                @if ($recentComments->isEmpty())
                    <div class="p-6">
                        <x-empty-state icon="message" title="No comments yet" description="Comments from readers will appear here." />
                    </div>
                @else
                    <ul class="divide-y divide-line">
                        @foreach ($recentComments as $comment)
                            <li class="flex items-start gap-3 px-6 py-3.5 transition-colors hover:bg-surface-alt/50">
                                <x-avatar :user="$comment->user" size="sm" class="mt-0.5" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-content">
                                        {{ $comment->user?->name ?? 'Guest' }}
                                        <span class="font-normal text-muted">on</span>
                                        <a href="{{ route('admin.posts.edit', $comment->post_id) }}" class="text-primary hover:underline">{{ $comment->post?->title }}</a>
                                    </p>
                                    <p class="mt-0.5 line-clamp-2 text-xs text-content-soft">{{ $comment->body }}</p>
                                </div>
                                <x-badge variant="{{ $comment->status_badge }}">{{ $comment->status_label }}</x-badge>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-card>
        </div>
    </div>
</x-admin-layout>
