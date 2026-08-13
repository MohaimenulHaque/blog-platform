@props(['href' => null, 'withText' => true])

@php
    $siteName   = setting('general.site_name', config('app.name'));
    $tagline    = setting('general.tagline');
    $logo       = setting('branding.logo');
@endphp

<a href="{{ $href ?? route('home') }}" {{ $attributes->class('inline-flex items-center gap-2.5') }} aria-label="{{ $siteName }}">
    @if ($logo)
        <img src="{{ asset('storage/'.$logo) }}" alt="{{ $siteName }} logo" class="h-10 w-auto">
    @else
        <span class="grid h-10 w-10 place-items-center rounded-xl bg-primary text-primary-fg shadow-soft transition-transform duration-300 group-hover:scale-105">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 19l7-7 3 3-7 7-3-3z" />
                <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z" />
                <path d="M2 2l7.586 7.586" />
                <circle cx="11" cy="11" r="2" />
            </svg>
        </span>
    @endif

    @if ($withText)
        <span class="flex flex-col leading-none">
            <span class="font-serif text-lg font-bold tracking-tight text-content">
                {{ $siteName }}
            </span>
            @if ($tagline)
                <span class="mt-0.5 text-[10px] font-semibold uppercase tracking-[0.22em] text-muted">{{ $tagline }}</span>
            @endif
        </span>
    @endif
</a>
