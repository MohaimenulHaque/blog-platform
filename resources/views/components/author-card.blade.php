@props([
    'name',
    'role',
    'bio',
    'href' => '#',
    'user' => null,
])

<div {{ $attributes->class('card card-hover flex flex-col items-center p-6 text-center') }}>
    @if ($user)
        <x-avatar :user="$user" size="xl" class="ring-4 ring-line/50" />
    @else
        <span class="grid h-20 w-20 place-items-center rounded-full bg-gradient-to-br from-primary to-secondary font-serif text-xl font-bold text-white shadow-soft">
            {{ collect(explode(' ', $name))->take(2)->map(fn ($w) => strtoupper(mb_substr($w, 0, 1)))->join('') }}
        </span>
    @endif

    <h3 class="mt-4 font-serif text-lg font-semibold text-content">
        <a href="{{ $href }}" class="transition-colors hover:text-primary">{{ $name }}</a>
    </h3>
    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-primary">{{ $role }}</p>
    <p class="mt-3 text-sm leading-relaxed text-muted text-pretty">{{ $bio }}</p>
</div>
