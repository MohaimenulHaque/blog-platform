<x-admin-layout>
    <x-slot name="title">{{ $post->title }}</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-4">
                @if ($post->featured_image_url)
                    <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="h-16 w-24 rounded-xl object-cover shadow-soft">
                @endif
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">{{ $post->title }}</h1>
                        <x-badge :variant="$post->status_badge">{{ $post->status_label }}</x-badge>
                        <x-badge :variant="$post->visibility_badge">{{ $post->visibility_label }}</x-badge>
                    </div>
                    <p class="mt-1 text-sm text-muted">
                        by {{ $post->author?->name }} · {{ $post->reading_time }} min read ·
                        <a href="{{ $post->url }}" target="_blank" rel="noopener" class="text-primary hover:underline">View on site →</a>
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @can('publish', $post)
                    @if ($post->status !== 'published')
                        <form method="POST" action="{{ route('admin.posts.publish', $post) }}">
                            @csrf
                            <x-button variant="success" size="md" type="submit">Publish</x-button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.posts.unpublish', $post) }}">
                            @csrf
                            <x-button variant="outline" size="md" type="submit">Unpublish</x-button>
                        </form>
                    @endif
                @endcan

                @can('schedule', $post)
                    @if ($post->status !== 'scheduled')
                        <x-button variant="secondary" size="md" type="button" x-data x-on:click="$dispatch('open-modal', 'schedule-post')">Schedule</x-button>
                    @endif
                @endcan

                @can('archive', $post)
                    @if ($post->status !== 'archived')
                        <form method="POST" action="{{ route('admin.posts.archive', $post) }}">
                            @csrf
                            <x-button variant="ghost" size="md" type="submit">Archive</x-button>
                        </form>
                    @endif
                @endcan

                @can('update', $post)
                    @if ($post->status !== 'draft')
                        <form method="POST" action="{{ route('admin.posts.draft', $post) }}">
                            @csrf
                            <x-button variant="ghost" size="md" type="submit">Move to draft</x-button>
                        </form>
                    @endif
                @endcan

                <x-button variant="primary" size="md" href="{{ route('admin.posts.edit', $post) }}">Edit</x-button>

                @can('delete', $post)
                    <x-button variant="danger" size="md" type="button" x-data x-on:click="$dispatch('open-modal', 'delete-post')">Delete</x-button>
                @endcan
            </div>
        </div>

        @if (session('status'))
            <x-alert type="success" :dismissible="true">{{ session('status') }}</x-alert>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <x-card class="overflow-hidden">
                    <div class="grid gap-4 border-b border-line bg-surface-alt/50 p-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-muted">Published</p>
                            <p class="mt-0.5 font-medium text-content">{{ $post->published_at?->format('M j, Y · g:i A') ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-muted">Scheduled</p>
                            <p class="mt-0.5 font-medium text-content">{{ $post->scheduled_at?->format('M j, Y · g:i A') ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-muted">Views</p>
                            <p class="mt-0.5 font-medium text-content">{{ number_format($post->view_count) }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-muted">Reading time</p>
                            <p class="mt-0.5 font-medium text-content">{{ $post->reading_time }} min</p>
                        </div>
                    </div>

                    <div class="p-6">
                        @if ($post->excerpt)
                            <p class="mb-6 border-l-4 border-primary-soft pl-4 text-lg italic text-content-soft">{{ $post->excerpt }}</p>
                        @endif

                        @if ($post->content)
                            <div class="prose-blog">{!! $post->content !!}</div>
                        @else
                            <p class="text-muted">This post has no content yet.</p>
                        @endif
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card class="p-5">
                    <h2 class="mb-4 font-serif text-sm font-semibold text-content">Details</h2>

                    <dl class="space-y-3 text-sm">
                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-muted">Author</dt>
                            <dd class="flex items-center gap-2 text-right">
                                <x-avatar :user="$post->author" size="xs" />
                                <span class="font-medium text-content">{{ $post->author?->name }}</span>
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-muted">Category</dt>
                            <dd class="font-medium text-content">{{ $post->category?->name ?? '—' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-muted">Slug</dt>
                            <dd class="break-all text-right font-medium text-content">{{ $post->slug }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-muted">UUID</dt>
                            <dd class="break-all text-right font-mono text-xs text-muted">{{ $post->uuid }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-muted">Created</dt>
                            <dd class="font-medium text-content">{{ $post->created_at->format('M j, Y') }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-muted">Updated</dt>
                            <dd class="font-medium text-content">{{ $post->updated_at->format('M j, Y') }}</dd>
                        </div>
                    </dl>
                </x-card>

                @if ($post->tags->isNotEmpty())
                    <x-card class="p-5">
                        <h2 class="mb-3 font-serif text-sm font-semibold text-content">Tags</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($post->tags as $tag)
                                <a href="{{ route('admin.tags.edit', $tag) }}" class="inline-flex items-center rounded-full bg-surface-alt px-3 py-1 text-xs font-medium text-content-soft transition-colors hover:bg-primary-soft hover:text-primary">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    </x-card>
                @endif
            </div>
        </div>
    </div>

    @can('schedule', $post)
        <x-modal name="schedule-post" maxWidth="md" focusable>
            <form method="POST" action="{{ route('admin.posts.schedule', $post) }}" class="p-6">
                @csrf
                <h2 class="font-serif text-lg font-semibold text-content">Schedule post</h2>
                <p class="mt-1 text-sm text-muted">Choose when this post should go live.</p>

                <div class="mt-4">
                    <label for="schedule_datetime" class="label">Publish at <span class="text-danger">*</span></label>
                    <input id="schedule_datetime" type="datetime-local" name="scheduled_at" class="input-field" required>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-button variant="ghost" size="md" type="button" x-data x-on:click="$dispatch('close')">Cancel</x-button>
                    <x-button variant="primary" size="md" type="submit">Schedule</x-button>
                </div>
            </form>
        </x-modal>
    @endcan

    @can('delete', $post)
        <x-modal name="delete-post" maxWidth="sm" focusable>
            <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="p-6">
                @csrf
                @method('DELETE')
                <div class="flex items-start gap-4">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-danger-soft text-danger">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6" /><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /></svg>
                    </span>
                    <div>
                        <h2 class="font-serif text-lg font-semibold text-content">Delete post?</h2>
                        <p class="mt-1 text-sm text-muted">This moves the post to the trash. You can restore it from the trash at any time.</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-button variant="ghost" size="md" type="button" x-data x-on:click="$dispatch('close')">Cancel</x-button>
                    <x-button variant="danger" size="md" type="submit">Delete</x-button>
                </div>
            </form>
        </x-modal>
    @endcan
</x-admin-layout>
