<x-admin-layout>
    <x-slot name="title">{{ __('Trashed comments') }}</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <x-admin-breadcrumb :items="[
                    ['label' => 'Comments', 'url' => route('admin.comments.index')],
                    ['label' => 'Trash'],
                ]" />
                <h1 class="mt-2 font-serif text-2xl font-semibold tracking-tight text-content">Trashed comments</h1>
                <p class="mt-0.5 text-sm text-muted">Restore or permanently delete deleted comments.</p>
            </div>
            <x-button variant="ghost" size="md" href="{{ route('admin.comments.index') }}">
                <x-icon icon="reply" class="h-4 w-4" />
                Back to comments
            </x-button>
        </div>

        @if (session('status'))
            <x-alert type="success" :dismissible="true">{{ session('status') }}</x-alert>
        @endif

        <x-card>
            <form method="GET" action="{{ route('admin.comments.trashed') }}" class="flex flex-col gap-3 border-b border-line p-4 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-muted">
                        <x-icon icon="search" class="h-4 w-4" />
                    </span>
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Search trashed comments…"
                        class="input-field pl-10"
                    >
                </div>

                <div class="flex gap-2">
                    <x-button variant="primary" size="md" type="submit">Search</x-button>
                    @if ($search)
                        <x-button variant="ghost" size="md" href="{{ route('admin.comments.trashed') }}">Reset</x-button>
                    @endif
                </div>
            </form>

            @if ($comments->isEmpty())
                <x-empty-state
                    icon="message"
                    title="Trash is empty"
                    :description="$search
                        ? 'No trashed comments match your search.'
                        : 'Deleted comments will appear here so you can restore or permanently remove them.'"
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
                                        <span class="line-clamp-1 max-w-[20rem] font-medium text-content-soft">{{ $comment->post?->title ?? 'Deleted post' }}</span>
                                    </div>

                                    <p class="mt-2 text-sm leading-relaxed text-content-soft">{{ $comment->body }}</p>

                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-muted">
                                        <time datetime="{{ $comment->created_at?->toIso8601String() }}">Deleted {{ $comment->deleted_at?->diffForHumans() }}</time>
                                        @if ($comment->parent_id)
                                            <span>· reply</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center gap-2 md:items-end">
                                <form method="POST" action="{{ route('admin.comments.restore', $comment) }}">
                                    @csrf
                                    <x-button variant="success" size="sm" type="submit">
                                        <x-icon icon="reply" class="h-3.5 w-3.5" />
                                        Restore
                                    </x-button>
                                </form>

                                <form method="POST" action="{{ route('admin.comments.force-destroy', $comment) }}" onsubmit="return confirm('Permanently delete this comment? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <x-button variant="danger" size="sm" type="submit">
                                        <x-icon icon="trash" class="h-3.5 w-3.5" />
                                        Delete forever
                                    </x-button>
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
