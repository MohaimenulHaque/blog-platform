@props(['url' => '#', 'label' => 'Social link', 'icon' => null])

<a
    href="{{ $url }}"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="{{ $label }}"
    {{ $attributes->class('grid h-10 w-10 place-items-center rounded-xl border border-line bg-surface text-muted transition-all duration-200 hover:-translate-y-0.5 hover:border-primary-ring hover:text-primary hover:shadow-soft') }}
>
    <x-icon :icon="$icon ?? strtolower($label)" class="h-4 w-4" />
</a>
