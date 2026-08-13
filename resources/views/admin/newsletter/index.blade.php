<x-admin-layout>
    <x-slot name="title">{{ __('Newsletter subscribers') }}</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">Newsletter</h1>
                <p class="mt-0.5 text-sm text-muted">Manage subscribers to the newsletter.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-button variant="outline" size="md" href="{{ route('admin.newsletter.export') }}">
                    Export CSV
                </x-button>
            </div>
        </div>

        @if (session('status'))
            <x-alert type="success" :dismissible="true">{{ session('status') }}</x-alert>
        @endif

        <div class="grid gap-4 sm:grid-cols-3">
            <x-card class="p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted">Total</p>
                <p class="mt-1 font-serif text-2xl font-semibold text-content">{{ number_format($total) }}</p>
            </x-card>
            <x-card class="p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted">Subscribed</p>
                <p class="mt-1 font-serif text-2xl font-semibold text-success">{{ number_format($subscribedCount) }}</p>
            </x-card>
            <x-card class="p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted">Unsubscribed</p>
                <p class="mt-1 font-serif text-2xl font-semibold text-muted">{{ number_format($total - $subscribedCount) }}</p>
            </x-card>
        </div>

        <x-card>
            <form method="GET" action="{{ route('admin.newsletter.index') }}" class="flex flex-col gap-3 border-b border-line p-4 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-muted">
                        <x-icon icon="search" class="h-4 w-4" />
                    </span>
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Search subscribers…"
                        class="input-field pl-10"
                    >
                </div>

                <x-select
                    name="status"
                    :options="['subscribed' => 'Subscribed', 'unsubscribed' => 'Unsubscribed']"
                    :selected="$filter"
                    emptyOption="All statuses"
                    class="sm:w-44"
                />

                <div class="flex gap-2">
                    <x-button variant="primary" size="md" type="submit">Filter</x-button>
                    @if ($search || $filter)
                        <x-button variant="ghost" size="md" href="{{ route('admin.newsletter.index') }}">Reset</x-button>
                    @endif
                </div>
            </form>

            @if ($subscribers->isEmpty())
                <x-empty-state
                    icon="mail"
                    title="No subscribers found"
                    :description="$search || $filter
                        ? 'Try adjusting your search or filters.'
                        : 'No one has subscribed to the newsletter yet.'"
                />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-line text-xs uppercase tracking-wider text-muted">
                                <th class="px-4 py-3 font-semibold">Email</th>
                                <th class="hidden px-4 py-3 font-semibold lg:table-cell">Name</th>
                                <th class="px-4 py-3 font-semibold">Status</th>
                                <th class="hidden px-4 py-3 font-semibold md:table-cell">Subscribed</th>
                                <th class="px-4 py-3 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach ($subscribers as $subscriber)
                                <tr class="transition-colors hover:bg-surface-alt/50">
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-content">{{ $subscriber->email }}</p>
                                    </td>
                                    <td class="hidden px-4 py-3 text-content-soft lg:table-cell">
                                        {{ $subscriber->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($subscriber->isSubscribed())
                                            <x-badge variant="success">Subscribed</x-badge>
                                        @else
                                            <x-badge variant="neutral">Unsubscribed</x-badge>
                                        @endif
                                    </td>
                                    <td class="hidden whitespace-nowrap px-4 py-3 text-muted md:table-cell">
                                        {{ $subscriber->created_at?->format('M j, Y') }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <form method="POST" action="{{ route('admin.newsletter.destroy', $subscriber) }}" onsubmit="return confirm('Remove this subscriber?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="grid h-8 w-8 place-items-center rounded-lg text-muted transition-colors hover:bg-danger-soft hover:text-danger" title="Remove" aria-label="Remove {{ $subscriber->email }}">
                                                <x-icon icon="trash" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-line p-4">
                    <x-pagination :paginator="$subscribers" />
                </div>
            @endif
        </x-card>
    </div>
</x-admin-layout>
