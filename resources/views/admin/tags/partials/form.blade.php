@php
    $old = fn (string $key, $default = null) => old($key, $tag?->{$key} ?? $default);
@endphp

<x-card class="space-y-5 p-5">
    <div>
        <label for="name" class="label">Name <span class="text-danger">*</span></label>
        <input
            id="name"
            type="text"
            name="name"
            value="{{ $old('name') }}"
            placeholder="e.g. Laravel"
            required
            @class(['input-field', '!border-danger focus:!ring-danger' => $errors->has('name')])
        >
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>

    <div>
        <label for="slug" class="label">Slug <span class="font-normal text-muted">(optional — auto-generated from name)</span></label>
        <input
            id="slug"
            type="text"
            name="slug"
            value="{{ $old('slug') }}"
            placeholder="laravel"
            @class(['input-field', '!border-danger focus:!ring-danger' => $errors->has('slug')])
        >
        <x-input-error :messages="$errors->get('slug')" class="mt-1" />
    </div>
</x-card>
