<x-admin-layout>
    <x-slot name="title">{{ __('Comment moderation') }}</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">Comments</h1>
                <p class="mt-0.5 text-sm text-muted">Review, approve or reject reader comments.</p>
            </div>
            <x-button variant="ghost" size="md" href="{{ route('admin.comments.trashed') }}">
                <x-icon icon="trash" class="h-4 w-4" />
                Trash
            </x-button>
        </div>

        @if (session('status'))
            <x-alert type="success" :dismissible="true">{{ session('status') }}</x-alert>
        @endif

        <x-card>
            <form method="GET" action="{{ route('admin.comments.index') }}" class="flex flex-col gap-3 border-b border-line p-4 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-muted">
                        <x-icon icon="search" class="h-4 w-4" />
                    </span>
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Search comments…"
                        class="input-field pl-10"
                    >
                </div>

                <div class="grid grid-cols-2 gap-3 sm:flex">
                    <x-select
                        name="status"
                        :options="collect($statuses)->mapWithKeys(fn ($s) => [$s->value => $s->label()])->all()"
                        :selected="$filter"
                        emptyOption="All statuses"
                        class="sm:w-40"
                    />
                </div>

                <div class="flex gap-2">
                    <x-button variant="primary" size="md" type="submit">Filter</x-button>
                    @if ($search || $filter)
                        <x-button variant="ghost" size="md" href="{{ route('admin.comments.index') }}">Reset</x-button>
                    @endif
                </div>
            </form>

            @if ($comments->isEmpty())
                <x-empty-state
                    icon="message"
                    title="No comments found"
                    :description="$search || $filter
                        ? 'Try adjusting your search or filters.'
                        : 'There are no comments to moderate yet.'"
                />
            @else
                <div class="divide-y divide-line">
                    @foreach ($comments as $comment)
                        <div class="flex flex-col gap-4 p-5 md:flex-row md:items-start">
                            <div class="flex min-w-0 flex-1 items-start gap-3">
                                <x-avatar :user="$comment->user" size="sm" class="mt-1 shrink-0" />
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2 text-sm">
                                        <span class="font-semibold text-content">{{ $comment->user?->name ?? 'Deleted user' }}</span>
                                        <span class="text-muted">on</span>
                                        <a href="{{ route('blog.show', $comment->post?->slug) }}" target="_blank" class="line-clamp-1 max-w-[20rem] font-medium text-primary hover:underline">
                                            {{ $comment->post?->title }}
                                        </a>
                                    </div>

                                    <p class="mt-2 text-sm leading-relaxed text-content-soft">{{ $comment->body }}</p>

                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-muted">
                                        <time datetime="{{ $comment->created_at?->toIso8601String() }}">{{ $comment->created_at?->diffForHumans() }}</time>
                                        @if ($comment->parent_id)
                                            <span>· reply</span>
                                        @endif
                                        @if ($comment->ip_address)
                                            <span>· {{ $comment->ip_address }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex shrink-0 flex-col gap-2 md:items-end">
                                <div class="flex items-center gap-2">
                                    <x-badge :variant="$comment->status_badge">{{ $comment->status_label }}</x-badge>
                                </div>

                                <form method="POST" action="{{ route('admin.comments.status', $comment) }}" class="flex flex-wrap gap-1.5 md:justify-end">
                                    @csrf
                                    @method('PATCH')

                                    @foreach ($statuses as $status)
                                        @if ($status->value !== $comment->status)
                                            <button
                                                type="submit"
                                                name="status"
                                                value="{{ $status->value }}"
                                                class="rounded-lg border border-line px-2.5 py-1 text-xs font-medium text-muted transition-colors hover:border-primary hover:text-primary"
                                            >
                                                {{ $status->label() }}
                                            </button>
                                        @endif
                                    @endforeach
                                </form>

                                <form method="POST" action="{{ route('admin.comments.destroy', $comment) }}" onsubmit="return confirm('Delete this comment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 text-xs font-medium text-danger hover:underline">
                                        <x-icon icon="trash" class="h-3.5 w-3.5" />
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-line p-4">
                    <x-pagination :paginator="$comments" />
                </div>
            @endif
        </x-card>
    </div>
</x-admin-layout>
