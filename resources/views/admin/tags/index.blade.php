<x-admin-layout>
    <x-slot name="title">{{ __('Tags') }}</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">Tags</h1>
                <p class="mt-0.5 text-sm text-muted">Lightweight labels applied to posts.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <x-button
                    variant="{{ $trashed ? 'primary' : 'outline' }}"
                    size="md"
                    href="{{ route('admin.tags.index', ['trashed' => ! $trashed]) }}"
                >
                    {{ $trashed ? 'Active tags' : 'Trash' }}
                </x-button>
                @if (! $trashed)
                    <x-button variant="primary" size="md" href="{{ route('admin.tags.create') }}">New tag</x-button>
                @endif
            </div>
        </div>

        @if (session('status'))
            <x-alert type="success" :dismissible="true">{{ session('status') }}</x-alert>
        @endif

        <x-card>
            <form method="GET" action="{{ route('admin.tags.index') }}" class="border-b border-line p-4">
                <input type="hidden" name="trashed" value="{{ $trashed ? '1' : '' }}">
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-muted">
                        <x-icon icon="search" class="h-4 w-4" />
                    </span>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search tags…"
                        class="input-field pl-10"
                    >
                </div>
            </form>

            @if ($tags->isEmpty())
                <x-empty-state
                    icon="inbox"
                    :title="$trashed ? 'Trash is empty' : 'No tags yet'"
                    :description="$trashed ? 'Deleted tags will appear here.' : 'Create your first tag to start labelling posts.'"
                >
                    @if (! $trashed)
                        <x-slot name="action">
                            <x-button variant="primary" href="{{ route('admin.tags.create') }}">New tag</x-button>
                        </x-slot>
                    @endif
                </x-empty-state>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-line text-xs uppercase tracking-wider text-muted">
                                <th class="px-4 py-3 font-semibold">Name</th>
                                <th class="hidden px-4 py-3 font-semibold md:table-cell">Slug</th>
                                <th class="hidden px-4 py-3 font-semibold md:table-cell">Posts</th>
                                <th class="px-4 py-3 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach ($tags as $tag)
                                <tr class="transition-colors hover:bg-surface-alt/50">
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full bg-surface-alt px-3 py-1 font-medium text-content">#{{ $tag->name }}</span>
                                    </td>
                                    <td class="hidden px-4 py-3 font-mono text-xs text-muted md:table-cell">{{ $tag->slug }}</td>
                                    <td class="hidden px-4 py-3 text-content-soft md:table-cell">{{ $tag->posts_count }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            @if ($trashed)
                                                <form method="POST" action="{{ route('admin.tags.restore', $tag) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-button variant="soft" size="sm" type="submit">Restore</x-button>
                                                </form>
                                            @else
                                                <a href="{{ route('admin.tags.edit', $tag) }}" class="grid h-8 w-8 place-items-center rounded-lg text-muted transition-colors hover:bg-surface-alt hover:text-content" title="Edit" aria-label="Edit {{ $tag->name }}">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z" /></svg>
                                                </a>
                                                <x-button variant="danger" size="sm" type="button" x-data x-on:click="$dispatch('open-modal', 'delete-tag-{{ $tag->id }}')">Delete</x-button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-line p-4">
                    <x-pagination :paginator="$tags" />
                </div>
            @endif
        </x-card>
    </div>

    @foreach ($tags as $tag)
        @if (! $trashed)
            <x-modal name="delete-tag-{{ $tag->id }}" maxWidth="sm" focusable>
                <form method="POST" action="{{ route('admin.tags.destroy', $tag) }}" class="p-6">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-start gap-4">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-danger-soft text-danger">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6" /><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /></svg>
                        </span>
                        <div>
                            <h2 class="font-serif text-lg font-semibold text-content">Delete tag?</h2>
                            <p class="mt-1 text-sm text-muted">This moves the tag to the trash and detaches it from posts.</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <x-button variant="ghost" size="md" type="button" x-data x-on:click="$dispatch('close')">Cancel</x-button>
                        <x-button variant="danger" size="md" type="submit">Delete</x-button>
                    </div>
                </form>
            </x-modal>
        @endif
    @endforeach
</x-admin-layout>
