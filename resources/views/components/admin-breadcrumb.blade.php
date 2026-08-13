@props([
    'items' => [],
])

<nav aria-label="Breadcrumb" {{ $attributes->merge(['class' => 'flex items-center gap-1.5 text-xs text-muted']) }}>
    <a href="{{ route('admin.dashboard') }}" class="transition-colors hover:text-primary">Dashboard</a>

    @foreach ($items as $label => $url)
        <svg class="h-3.5 w-3.5 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6" /></svg>

        @if (is_string($url))
            <a href="{{ $url }}" class="transition-colors hover:text-primary">{{ $label }}</a>
        @else
            <span class="font-medium text-content-soft" aria-current="page">{{ $label }}</span>
        @endif
    @endforeach
</nav>
