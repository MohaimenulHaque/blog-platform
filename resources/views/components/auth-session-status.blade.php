@props(['status'])

@if ($status)
    <div {{ $attributes->class(['rounded-xl border border-success/40 bg-success-soft px-4 py-3 text-sm font-medium text-success']) }}>
        {{ $status }}
    </div>
@endif
