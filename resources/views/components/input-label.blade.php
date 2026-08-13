@props(['value'])

<label {{ $attributes->class(['label']) }}>
    {{ $value ?? $slot }}
</label>
