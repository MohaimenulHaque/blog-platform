@props([
    'eyebrow' => null,
    'title',
    'description' => null,
])

<div {{ $attributes->class('flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between') }}>
    <div class="max-w-2xl">
        @if ($eyebrow)
            <p class="eyebrow animate-fade-up">
                <span class="h-px w-6 bg-current"></span>
                {{ $eyebrow }}
            </p>
        @endif

        <h2 class="section-title mt-3 animate-fade-up" style="animation-delay: 60ms">{{ $title }}</h2>

        @if ($description)
            <p class="section-subtitle animate-fade-up" style="animation-delay: 120ms">{{ $description }}</p>
        @endif
    </div>

    @isset($action)
        <div class="shrink-0 animate-fade-up" style="animation-delay: 180ms">{{ $action }}</div>
    @endisset
</div>
