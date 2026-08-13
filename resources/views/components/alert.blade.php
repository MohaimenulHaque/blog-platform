@props(['type' => 'info', 'title' => null, 'dismissible' => false])

@php
    $tones = [
        'success' => ['wrap' => 'border-success/40 bg-success-soft text-success', 'icon' => 'text-success'],
        'warning' => ['wrap' => 'border-warning/40 bg-warning-soft text-warning', 'icon' => 'text-warning'],
        'danger' => ['wrap' => 'border-danger/40 bg-danger-soft text-danger', 'icon' => 'text-danger'],
        'info' => ['wrap' => 'border-info/40 bg-info-soft text-info', 'icon' => 'text-info'],
    ];

    $tone = $tones[$type] ?? $tones['info'];

    $icons = [
        'success' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14" /><polyline points="22 4 12 14.01 9 11.01" /></svg>',
        'warning' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" /><line x1="12" y1="9" x2="12" y2="13" /><line x1="12" y1="17" x2="12.01" y2="17" /></svg>',
        'danger' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10" /><line x1="15" y1="9" x2="9" y2="15" /><line x1="9" y1="9" x2="15" y2="15" /></svg>',
        'info' => '<svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10" /><line x1="12" y1="16" x2="12" y2="12" /><line x1="12" y1="8" x2="12.01" y2="8" /></svg>',
    ];
@endphp

<div
    x-data="{ visible: true }"
    x-show="visible"
    x-transition
    role="alert"
    {{ $attributes->class(['rounded-xl border p-4 text-sm', $tone['wrap']]) }}
>
    <div class="flex items-start gap-3">
        <span class="{{ $tone['icon'] }} mt-0.5 shrink-0">{!! $icons[$type] !!}</span>

        <div class="min-w-0 flex-1">
            @if ($title)
                <p class="font-semibold">{{ $title }}</p>
            @endif

            <div @if ($title) class="mt-0.5" @endif>{{ $slot }}</div>
        </div>

        @if ($dismissible)
            <button type="button" x-on:click="visible = false" class="shrink-0 opacity-60 transition-opacity hover:opacity-100" aria-label="Dismiss">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" /></svg>
            </button>
        @endif
    </div>
</div>
