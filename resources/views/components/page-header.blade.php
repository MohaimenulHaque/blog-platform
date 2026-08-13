@props([
    'eyebrow' => null,
    'title',
    'description' => null,
])

<section class="border-b border-line bg-surface">
    <x-container class="py-12 md:py-16">
        @if ($eyebrow)
            <p class="eyebrow animate-fade-up">
                <span class="h-px w-6 bg-current"></span>
                {{ $eyebrow }}
            </p>
        @endif

        <h1 class="mt-3 max-w-3xl animate-fade-up font-serif text-4xl font-semibold tracking-tight text-content text-balance md:text-5xl" style="animation-delay: 60ms">
            {{ $title }}
        </h1>

        @if ($description)
            <p class="mt-4 max-w-2xl animate-fade-up text-lg text-muted text-pretty" style="animation-delay: 120ms">
                {{ $description }}
            </p>
        @endif

        @isset($actions)
            <div class="mt-6 flex flex-wrap gap-3 animate-fade-up" style="animation-delay: 180ms">
                {{ $actions }}
            </div>
        @endisset
    </x-container>
</section>
