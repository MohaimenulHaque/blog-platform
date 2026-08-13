<x-admin-layout>
    <x-slot name="title">{{ __('Authors') }}</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">Authors</h1>
                <p class="mt-0.5 text-sm text-muted">People who write for the blog.</p>
            </div>
            <x-button variant="primary" size="md" href="{{ route('admin.authors.create') }}">New author</x-button>
        </div>

        @if (session('status'))
            <x-alert type="success" :dismissible="true">{{ session('status') }}</x-alert>
        @endif

        <x-card>
            <form method="GET" action="{{ route('admin.authors.index') }}" class="border-b border-line p-4">
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-muted">
                        <x-icon icon="search" class="h-4 w-4" />
                    </span>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search authors…"
                        class="input-field pl-10"
                    >
                </div>
            </form>

            @if ($authors->isEmpty())
                <x-empty-state
                    icon="users"
                    title="No authors yet"
                    description="Add an author to start assigning posts."
                >
                    <x-slot name="action">
                        <x-button variant="primary" href="{{ route('admin.authors.create') }}">New author</x-button>
                    </x-slot>
                </x-empty-state>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-line text-xs uppercase tracking-wider text-muted">
                                <th class="px-4 py-3 font-semibold">Author</th>
                                <th class="hidden px-4 py-3 font-semibold md:table-cell">Designation</th>
                                <th class="hidden px-4 py-3 font-semibold md:table-cell">Posts</th>
                                <th class="px-4 py-3 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach ($authors as $author)
                                <tr class="transition-colors hover:bg-surface-alt/50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <x-avatar :user="$author" size="sm" />
                                            <div>
                                                <p class="font-semibold text-content">{{ $author->name }}</p>
                                                <p class="text-xs text-muted">{{ $author->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden px-4 py-3 text-content-soft md:table-cell">{{ $author->designation ?? '—' }}</td>
                                    <td class="hidden px-4 py-3 text-content-soft md:table-cell">
                                        {{ $author->posts_count }} <span class="text-xs text-muted">({{ $author->published_posts_count }} published)</span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.authors.edit', $author) }}" class="grid h-8 w-8 place-items-center rounded-lg text-muted transition-colors hover:bg-surface-alt hover:text-content" title="Edit" aria-label="Edit {{ $author->name }}">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z" /></svg>
                                            </a>
                                            <x-button variant="danger" size="sm" type="button" x-data x-on:click="$dispatch('open-modal', 'remove-author-{{ $author->id }}')">Remove</x-button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-line p-4">
                    <x-pagination :paginator="$authors" />
                </div>
            @endif
        </x-card>
    </div>

    @foreach ($authors as $author)
        <x-modal name="remove-author-{{ $author->id }}" maxWidth="sm" focusable>
            <form method="POST" action="{{ route('admin.authors.destroy', $author) }}" class="p-6">
                @csrf
                @method('DELETE')
                <div class="flex items-start gap-4">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-danger-soft text-danger">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 00-3-3.87" /><path d="M16 3.13a4 4 0 010 7.75" /></svg>
                    </span>
                    <div>
                        <h2 class="font-serif text-lg font-semibold text-content">Remove author role?</h2>
                        <p class="mt-1 text-sm text-muted">{{ $author->name }} will keep their account but no longer be able to write posts. Their existing posts stay intact.</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-button variant="ghost" size="md" type="button" x-data x-on:click="$dispatch('close')">Cancel</x-button>
                    <x-button variant="danger" size="md" type="submit">Remove role</x-button>
                </div>
            </form>
        </x-modal>
    @endforeach
</x-admin-layout>
