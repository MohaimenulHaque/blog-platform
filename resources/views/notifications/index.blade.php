<x-app-layout>
    <x-slot name="title">{{ __('Notifications') }}</x-slot>
    <x-slot name="noindex">true</x-slot>
    <x-slot name="metaDescription">{{ __('Your activity and updates.') }}</x-slot>

    <x-page-header
        eyebrow="Inbox"
        :title="__('Notifications')"
        :description="__('Comments, approvals and activity on your posts.')"
    />

    <section>
        <x-container class="py-12 md:py-16">
            <div class="mx-auto max-w-2xl">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-muted">
                        <strong class="text-content">{{ $notifications->total() }}</strong>
                        {{ Str::plural('notification', $notifications->total()) }}
                    </p>

                    @if ($notifications->whereNull('read_at')->isNotEmpty())
                        <form method="POST" action="{{ route('notifications.read-all') }}">
                            @csrf
                            <button type="submit" class="text-sm font-semibold text-primary hover:underline">
                                Mark all as read
                            </button>
                        </form>
                    @endif
                </div>

                @if (session('status'))
                    <x-alert type="success" :dismissible="true" class="mt-4">{{ session('status') }}</x-alert>
                @endif

                <div class="mt-6 space-y-3">
                    @forelse ($notifications as $notification)
                        @php
                            $data = $notification->data;
                            $isRead = $notification->read_at !== null;
                        @endphp

                        <div @class(['card flex gap-4 p-4 transition-colors', 'border-primary/40 bg-primary-soft/40' => ! $isRead])>
                            <span @class([
                                'grid h-10 w-10 shrink-0 place-items-center rounded-xl',
                                'bg-primary-soft text-primary' => ! $isRead,
                                'bg-surface-alt text-muted' => $isRead,
                            ])>
                                <x-icon :icon="$data['icon'] ?? 'bell'" class="h-5 w-5" />
                            </span>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <p class="text-sm font-semibold text-content">{{ $data['title'] ?? 'Notification' }}</p>
                                    <time class="shrink-0 text-xs text-muted" datetime="{{ $notification->created_at?->toIso8601String() }}">
                                        {{ $notification->created_at?->diffForHumans() }}
                                    </time>
                                </div>
                                <p class="mt-1 text-sm leading-relaxed text-muted">{{ $data['message'] ?? '' }}</p>

                                @if (isset($data['url']))
                                    <form method="POST" action="{{ route('notifications.read', $notification) }}" class="mt-3">
                                        @csrf
                                        <input type="hidden" name="next" value="{{ $data['url'] }}">
                                        <button type="submit" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline">
                                            View
                                            <x-icon icon="arrow-right" class="h-4 w-4" />
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-line py-14 text-center">
                            <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-surface-alt text-muted">
                                <x-icon icon="bell" class="h-6 w-6" />
                            </div>
                            <p class="mt-4 text-sm font-medium text-content-soft">{{ __("You're all caught up") }}</p>
                            <p class="mt-1 text-sm text-muted">No notifications yet.</p>
                        </div>
                    @endforelse
                </div>

                @if ($notifications->hasPages())
                    <div class="mt-8">
                        <x-pagination :paginator="$notifications" />
                    </div>
                @endif
            </div>
        </x-container>
    </section>
</x-app-layout>
