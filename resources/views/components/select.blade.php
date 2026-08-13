@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'emptyOption' => null,
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

    $isList = array_is_list($options);

    $selectedValue = (string) $selected;
@endphp

<div @class(['space-y-1.5', $wrapperClass])>
    @if ($label)
        <label for="{{ $id }}" class="label">{{ $label }}</label>
    @endif

    <select
        id="{{ $id }}"
        name="{{ $name }}"
        @if ($required) required @endif
        @disabled($disabled)
        @class([
            'input-field appearance-none bg-no-repeat pr-10',
            'bg-[url("data:image/svg+xml,%3Csvg%20xmlns=%27http://www.w3.org/2000/svg%27%20viewBox=%270%200%2024%2024%27%20fill=%27none%27%20stroke=%27currentColor%27%20stroke-width=%272%27%20stroke-linecap=%27round%27%20stroke-linejoin=%27round%27%3E%3Cpolyline%20points=%276%209%2012%2015%2018%209%27/%3E%3C/svg%3E")] bg-[right_0.75rem_center] bg-[length:1rem]',
            '!border-danger focus:!ring-danger' => $hasError,
        ])
    >
        @if ($placeholder)
            <option value="" {{ $selectedValue === '' ? 'selected' : '' }} disabled>{{ $placeholder }}</option>
        @endif

        @if ($emptyOption)
            <option value="" {{ $selectedValue === '' ? 'selected' : '' }}>{{ $emptyOption }}</option>
        @endif

        @foreach ($options as $key => $optionLabel)
            @php
                $value = $isList ? $optionLabel : $key;
            @endphp
            <option value="{{ $value }}" {{ $selectedValue === (string) $value ? 'selected' : '' }}>{{ $optionLabel }}</option>
        @endforeach
    </select>

    @if ($hasError)
        <p class="text-sm text-danger">
            {{ is_array($errorMessages) ? $errorMessages[0] : $errorMessages }}
        </p>
    @elseif ($help)
        <p class="text-sm text-muted">{{ $help }}</p>
    @endif
</div>
