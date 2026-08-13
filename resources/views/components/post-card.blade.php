@props([
    'title',
    'excerpt',
    'category',
    'date',
    'author',
    'readTime',
    'views' => null,
    'gradient' => 'ph-img',
    'href' => '#',
    'authorHref' => null,
    'image' => null,
    'user' => null,
])

<article {{ $attributes->class('card card-hover group relative flex flex-col overflow-hidden') }}>
    <div class="relative aspect-[16/10] overflow-hidden {{ $gradient }}">
        @if ($image)
            <img
                src="{{ $image }}"
                alt="{{ $title }}"
                loading="lazy"
                decoding="async"
                class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
            >
        @else
            <div class="absolute inset-0 flex items-center justify-center text-primary/20 transition-transform duration-500 group-hover:scale-110">
                <x-icon icon="pen" class="h-12 w-12" />
            </div>
        @endif

        <x-badge variant="primary" class="absolute left-4 top-4 z-10 shadow-soft">{{ $category }}</x-badge>
    </div>

    <div class="flex flex-1 flex-col p-5">
        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-muted">
            <span class="inline-flex items-center gap-1.5">
                <x-icon icon="calendar" class="h-3.5 w-3.5" />
                {{ $date }}
            </span>
            <span aria-hidden="true">·</span>
            <span class="inline-flex items-center gap-1.5">
                <x-icon icon="clock" class="h-3.5 w-3.5" />
                {{ $readTime }}
            </span>
            @if ($views !== null)
                <span aria-hidden="true">·</span>
                <span class="inline-flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" /></svg>
                    {{ number_format($views) }} views
                </span>
            @endif
        </div>

        <h3 class="mt-2.5 font-serif text-lg font-semibold leading-snug text-content transition-colors group-hover:text-primary">
            <a href="{{ $href }}" class="focus-visible:outline-none">
                <span class="absolute inset-0" aria-hidden="true"></span>
                {{ $title }}
            </a>
        </h3>

        <p class="mt-2 flex-1 text-sm leading-relaxed text-muted line-clamp-2">{{ $excerpt }}</p>

        <div class="mt-4 flex items-center gap-2.5 border-t border-line pt-4">
            @if ($user)
                <x-avatar :user="$user" size="sm" />
            @else
                <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-primary-soft text-xs font-bold text-primary">
                    {{ collect(explode(' ', $author))->take(2)->map(fn ($w) => strtoupper(mb_substr($w, 0, 1)))->join('') }}
                </span>
            @endif

            <span class="text-sm font-medium text-content-soft">
                @if ($authorHref)
                    <a href="{{ $authorHref }}" class="relative transition-colors hover:text-primary">{{ $author }}</a>
                @else
                    {{ $author }}
                @endif
            </span>

            <span class="ms-auto text-muted transition-transform duration-200 group-hover:translate-x-1 group-hover:text-primary" aria-hidden="true">
                <x-icon icon="arrow-right" class="h-4 w-4" />
            </span>
        </div>
    </div>
</article>
