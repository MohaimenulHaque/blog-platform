@props(['user' => null, 'size' => 'md'])

@php
    $user ??= auth()->user();

    $sizes = [
        'xs' => 'h-7 w-7 text-[10px]',
        'sm' => 'h-8 w-8 text-xs',
        'md' => 'h-10 w-10 text-sm',
        'lg' => 'h-14 w-14 text-base',
        'xl' => 'h-20 w-20 text-xl',
        '2xl' => 'h-28 w-28 text-3xl',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $initials = $user
        ? collect(explode(' ', $user->name))->take(2)->map(fn ($w) => strtoupper(mb_substr($w, 0, 1)))->join('')
        : '?';
@endphp

@if ($user && $user->avatar_url)
    <img
        src="{{ $user->avatar_url }}"
        alt="{{ $user->name }}"
        {{ $attributes->class(['shrink-0 rounded-full object-cover', $sizeClass]) }}
    >
@else
    <span {{ $attributes->class(['grid shrink-0 place-items-center rounded-full bg-primary font-bold text-primary-fg', $sizeClass]) }}>
        {{ $initials }}
    </span>
@endif
