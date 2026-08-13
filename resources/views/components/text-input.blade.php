@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->class(['input-field']) }}>
