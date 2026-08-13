@props(['as' => 'div', 'hover' => false, 'padded' => false])

@php
    $tag = $as;

    $classes = ['card'];

    if ($hover) {
        $classes[] = 'card-hover';
    }

    if ($padded) {
        $classes[] = 'p-6';
    }
@endphp

<{{ $tag }} {{ $attributes->class($classes) }}>
    {{ $slot }}
</{{ $tag }}>
