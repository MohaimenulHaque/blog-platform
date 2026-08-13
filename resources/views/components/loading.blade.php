@props(['label' => 'Loading…'])

<div role="status" aria-live="polite" {{ $attributes->class('flex flex-col items-center justify-center gap-4 py-16 text-center') }}>
    <span class="spinner"></span>
    @if ($label)
        <span class="text-sm font-medium text-muted">{{ $label }}</span>
    @endif
</div>
