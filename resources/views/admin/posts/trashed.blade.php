<x-admin-layout>
    <x-slot name="title">{{ __('Trash') }}</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">Trash</h1>
                <p class="mt-0.5 text-sm text-muted">Restore posts that were sent to the trash.</p>
            </div>
            <x-button variant="outline" size="md" href="{{ route('admin.posts.index') }}">Back to posts</x-button>
        </div>

        @if (session('status'))
            <x-alert type="success" :dismissible="true">{{ session('status') }}</x-alert>
        @endif

        <x-card>
            <form method="GET" action="{{ route('admin.posts.trashed') }}" class="border-b border-line p-4">
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-muted">
                        <x-icon icon="search" class="h-4 w-4" />
                    </span>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search trashed posts…"
                        class="input-field pl-10"
                    >
                </div>
            </form>

            @if ($posts->isEmpty())
                <x-empty-state
                    icon="inbox"
                    title="Trash is empty"
                    description="Deleted posts will appear here so you can restore them."
                />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-line text-xs uppercase tracking-wider text-muted">
                                <th class="px-4 py-3 font-semibold">Title</th>
                                <th class="hidden px-4 py-3 font-semibold md:table-cell">Author</th>
                                <th class="hidden px-4 py-3 font-semibold lg:table-cell">Deleted</th>
                                <th class="px-4 py-3 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach ($posts as $post)
                                <tr class="transition-colors hover:bg-surface-alt/50">
                                    <td class="px-4 py-3">
                                        <p class="font-semibold text-content">{{ $post->title }}</p>
                                        <p class="mt-0.5 line-clamp-1 max-w-md text-xs text-muted">{{ $post->excerpt }}</p>
                                    </td>
                                    <td class="hidden px-4 py-3 text-content-soft md:table-cell">{{ $post->author?->name }}</td>
                                    <td class="hidden px-4 py-3 text-muted lg:table-cell">{{ $post->deleted_at?->format('M j, Y · g:i A') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <form method="POST" action="{{ route('admin.posts.restore', $post) }}">
                                                @csrf
                                                @method('PATCH')
                                                <x-button variant="soft" size="sm" type="submit">Restore</x-button>
                                            </form>
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
