@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'autofocus' => false,
    'autocomplete' => null,
    'disabled' => false,
    'help' => null,
    'error' => null,
    'wrapperClass' => null,
])

@php
    $id = $id ?? $name;
    $errorMessages = $error ?? ($name ? $errors->get($name) : null);
    $hasError = ! empty($errorMessages);
@endphp

<div @class(['space-y-1.5', $wrapperClass])>
    @if ($label)
        <label for="{{ $id }}" class="label">{{ $label }}</label>
    @endif

    <input
        id="{{ $id }}"
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        @if ($required) required @endif
        @if ($autofocus) autofocus @endif
        autocomplete="{{ $autocomplete }}"
        @disabled($disabled)
        @class([
            'input-field',
            '!border-danger focus:!ring-danger' => $hasError,
        ])
    >

    @if ($hasError)
        <p class="text-sm text-danger">
            {{ is_array($errorMessages) ? $errorMessages[0] : $errorMessages }}
        </p>
    @elseif ($help)
        <p class="text-sm text-muted">{{ $help }}</p>
    @endif
</div>
