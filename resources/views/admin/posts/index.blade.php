<x-admin-layout>
    <x-slot name="title">{{ __('Posts') }}</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">Posts</h1>
                <p class="mt-0.5 text-sm text-muted">Create, publish and manage blog content.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-button variant="outline" size="md" href="{{ route('admin.posts.trashed', $filters) }}">
                    Trash
                </x-button>
                <x-button variant="primary" size="md" href="{{ route('admin.posts.create') }}">
                    New post
                </x-button>
            </div>
        </div>

        @if (session('status'))
            <x-alert type="success" :dismissible="true">{{ session('status') }}</x-alert>
        @endif

        @if (session('error'))
            <x-alert type="danger" :dismissible="true">{{ session('error') }}</x-alert>
        @endif

        <x-card>
            <form method="GET" action="{{ route('admin.posts.index') }}" class="flex flex-col gap-3 border-b border-line p-4 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-muted">
                        <x-icon icon="search" class="h-4 w-4" />
                    </span>
                    <input
                        type="text"
                        name="search"
                        value="{{ $filters['search'] }}"
                        placeholder="Search posts…"
                        class="input-field pl-10"
                    >
                </div>

                <div class="grid grid-cols-2 gap-3 sm:flex">
                    <x-select
                        name="status"
                        :options="collect($statuses)->mapWithKeys(fn ($s) => [$s->value => $s->label()])->all()"
                        :selected="$filters['status']"
                        placeholder="All statuses"
                        class="sm:w-40"
                    />
                    <x-select
                        name="category_id"
                        :options="$categories->pluck('name', 'id')->all()"
                        :selected="$filters['category_id']"
                        placeholder="All categories"
                        class="sm:w-44"
                    />
                </div>

                <div class="flex gap-2">
                    <x-button variant="primary" size="md" type="submit">Filter</x-button>
                    @if ($filters['search'] || $filters['status'] || $filters['category_id'])
                        <x-button variant="ghost" size="md" href="{{ route('admin.posts.index') }}">Reset</x-button>
                    @endif
                </div>
            </form>

            @if ($posts->isEmpty())
                <x-empty-state
                    icon="documents"
                    title="No posts found"
                    :description="$filters['search'] || $filters['status'] || $filters['category_id']
                        ? 'Try adjusting your search or filters.'
                        : 'Start writing your first post — it takes just a minute.'"
                >
                    <x-slot name="action">
                        <x-button variant="primary" href="{{ route('admin.posts.create') }}">Create post</x-button>
                    </x-slot>
                </x-empty-state>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-line text-xs uppercase tracking-wider text-muted">
                                <th class="px-4 py-3 font-semibold">Title</th>
                                <th class="px-4 py-3 font-semibold">Author</th>
                                <th class="hidden px-4 py-3 font-semibold lg:table-cell">Category</th>
                                <th class="px-4 py-3 font-semibold">Status</th>
                                <th class="hidden px-4 py-3 font-semibold md:table-cell">Date</th>
                                <th class="px-4 py-3 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach ($posts as $post)
                                <tr class="transition-colors hover:bg-surface-alt/50">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-content">
                                            <a href="{{ route('admin.posts.show', $post) }}" class="transition-colors hover:text-primary">
                                                {{ $post->title }}
                                            </a>
                                        </p>
                                        <p class="mt-0.5 line-clamp-1 max-w-md text-xs text-muted">{{ $post->excerpt }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <x-avatar :user="$post->author" size="xs" />
                                            <span class="text-content-soft">{{ $post->author?->name }}</span>
                                        </div>
                                    </td>
                                    <td class="hidden px-4 py-3 lg:table-cell">
                                        @if ($post->category)
                                            <a href="{{ route('admin.categories.edit', $post->category) }}" class="text-content-soft transition-colors hover:text-primary">{{ $post->category->name }}</a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <x-badge :variant="$post->status_badge">{{ $post->status_label }}</x-badge>
                                            @if ($post->visibility === 'private')
                                                <x-badge variant="neutral">Private</x-badge>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="hidden whitespace-nowrap px-4 py-3 text-muted md:table-cell">
                                        {{ $post->published_at?->format('M j, Y') ?? $post->updated_at->format('M j, Y') }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="{{ route('admin.posts.show', $post) }}" class="grid h-8 w-8 place-items-center rounded-lg text-muted transition-colors hover:bg-surface-alt hover:text-content" title="View" aria-label="View {{ $post->title }}">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" /></svg>
                                            </a>
                                            <a href="{{ route('admin.posts.edit', $post) }}" class="grid h-8 w-8 place-items-center rounded-lg text-muted transition-colors hover:bg-surface-alt hover:text-content" title="Edit" aria-label="Edit {{ $post->title }}">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z" /></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-line p-4">
                    <x-pagination :paginator="$posts" />
                </div>
            @endif
        </x-card>
    </div>
</x-admin-layout>
