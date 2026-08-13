<x-admin-layout>
    <x-slot name="title">{{ __('Categories') }}</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">Categories</h1>
                <p class="mt-0.5 text-sm text-muted">Organise posts into topics.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <x-button
                    variant="{{ $trashed ? 'primary' : 'outline' }}"
                    size="md"
                    href="{{ route('admin.categories.index', ['trashed' => ! $trashed]) }}"
                >
                    {{ $trashed ? 'Active categories' : 'Trash' }}
                </x-button>
                @if (! $trashed)
                    <x-button variant="primary" size="md" href="{{ route('admin.categories.create') }}">New category</x-button>
                @endif
            </div>
        </div>

        @if (session('status'))
            <x-alert type="success" :dismissible="true">{{ session('status') }}</x-alert>
        @endif

        @if (session('error'))
            <x-alert type="danger" :dismissible="true">{{ session('error') }}</x-alert>
        @endif

        <x-card>
            <form method="GET" action="{{ route('admin.categories.index') }}" class="border-b border-line p-4">
                <input type="hidden" name="trashed" value="{{ $trashed ? '1' : '' }}">
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-muted">
                        <x-icon icon="search" class="h-4 w-4" />
                    </span>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search categories…"
                        class="input-field pl-10"
                    >
                </div>
            </form>

            @if ($categories->isEmpty())
                <x-empty-state
                    icon="inbox"
                    :title="$trashed ? 'Trash is empty' : 'No categories yet'"
                    :description="$trashed ? 'Deleted categories will appear here.' : 'Create your first category to start organising posts.'"
                >
                    @if (! $trashed)
                        <x-slot name="action">
                            <x-button variant="primary" href="{{ route('admin.categories.create') }}">New category</x-button>
                        </x-slot>
                    @endif
                </x-empty-state>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-line text-xs uppercase tracking-wider text-muted">
                                <th class="px-4 py-3 font-semibold">Name</th>
                                <th class="hidden px-4 py-3 font-semibold md:table-cell">Posts</th>
                                <th class="px-4 py-3 font-semibold">Status</th>
                                <th class="px-4 py-3 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach ($categories as $category)
                                <tr class="transition-colors hover:bg-surface-alt/50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="ph-img grid h-10 w-10 shrink-0 place-items-center rounded-xl font-serif font-semibold text-primary">
                                                {{ mb_strtoupper(mb_substr($category->name, 0, 1)) }}
                                            </span>
                                            <div>
                                                <p class="font-semibold text-content">{{ $category->name }}</p>
                                                <p class="line-clamp-1 max-w-md text-xs text-muted">{{ $category->description }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden px-4 py-3 text-content-soft md:table-cell">{{ $category->posts_count }}</td>
                                    <td class="px-4 py-3">
                                        @if (! $trashed)
                                            <x-badge :variant="$category->status_badge">{{ $category->status_label }}</x-badge>
                                        @else
                                            <x-badge variant="danger">Deleted</x-badge>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            @if ($trashed)
                                                <form method="POST" action="{{ route('admin.categories.restore', $category) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <x-button variant="soft" size="sm" type="submit">Restore</x-button>
                                                </form>
                                            @else
                                                <a href="{{ route('admin.categories.edit', $category) }}" class="grid h-8 w-8 place-items-center rounded-lg text-muted transition-colors hover:bg-surface-alt hover:text-content" title="Edit" aria-label="Edit {{ $category->name }}">
                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z" /></svg>
                                                </a>
                                                <x-button variant="danger" size="sm" type="button" x-data x-on:click="$dispatch('open-modal', 'delete-category-{{ $category->id }}')">Delete</x-button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-line p-4">
                    <x-pagination :paginator="$categories" />
                </div>
            @endif
        </x-card>
    </div>

    @foreach ($categories as $category)
        @if (! $trashed)
            <x-modal name="delete-category-{{ $category->id }}" maxWidth="sm" focusable>
                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="p-6">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-start gap-4">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-danger-soft text-danger">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6" /><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" /><line x1="10" y1="11" x2="10" y2="17" /><line x1="14" y1="11" x2="14" y2="17" /></svg>
                        </span>
                        <div>
                            <h2 class="font-serif text-lg font-semibold text-content">Delete category?</h2>
                            <p class="mt-1 text-sm text-muted">This moves the category to the trash. Categories with posts cannot be deleted.</p>
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
