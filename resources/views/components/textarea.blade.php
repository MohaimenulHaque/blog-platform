@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'rows' => 4,
    'placeholder' => null,
    'required' => false,
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

    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if ($required) required @endif
        @disabled($disabled)
        @class([
            'input-field resize-y',
            '!border-danger focus:!ring-danger' => $hasError,
        ])
    >{{ $slot->isNotEmpty() ? $slot : old($name) }}</textarea>

    @if ($hasError)
        <p class="text-sm text-danger">
            {{ is_array($errorMessages) ? $errorMessages[0] : $errorMessages }}
        </p>
    @elseif ($help)
        <p class="text-sm text-muted">{{ $help }}</p>
    @endif
</div>
