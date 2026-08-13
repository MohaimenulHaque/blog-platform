@props([
    'name',
    'description',
    'count' => '0 articles',
    'gradient' => 'ph-img',
    'href' => '#',
    'image' => null,
])

<a
    href="{{ $href }}"
    {{ $attributes->class('card card-hover group flex flex-col overflow-hidden') }}
>
    <div class="relative h-24 overflow-hidden {{ $gradient }}">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $name }}" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent" aria-hidden="true"></div>
        @else
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="font-serif text-4xl font-semibold text-primary/25 transition-colors group-hover:text-primary/40 select-none">{{ mb_strtoupper(mb_substr($name, 0, 1)) }}</span>
            </div>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-5">
        <h3 class="font-serif text-lg font-semibold text-content transition-colors group-hover:text-primary">{{ $name }}</h3>
        <p class="mt-1 flex-1 text-sm leading-relaxed text-muted line-clamp-2">{{ $description }}</p>

        <div class="mt-4 flex items-center justify-between border-t border-line pt-3">
            <span class="text-xs font-medium text-muted">{{ $count }}</span>
            <span class="text-muted transition-all duration-200 group-hover:translate-x-1 group-hover:text-primary" aria-hidden="true">
                <x-icon icon="arrow-right" class="h-4 w-4" />
            </span>
        </div>
    </div>
</a>
