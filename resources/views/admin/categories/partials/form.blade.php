@php
    $old = fn (string $key, $default = null) => old($key, $category?->{$key} ?? $default);
@endphp

<div class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
        <x-card class="space-y-5 p-5">
            <div>
                <label for="name" class="label">Name <span class="text-danger">*</span></label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ $old('name') }}"
                    placeholder="e.g. Technology"
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
                    placeholder="technology"
                    @class(['input-field', '!border-danger focus:!ring-danger' => $errors->has('slug')])
                >
                <x-input-error :messages="$errors->get('slug')" class="mt-1" />
            </div>

            <div>
                <label for="description" class="label">Description</label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    placeholder="What kind of posts belong in this category?"
                    @class(['input-field resize-y', '!border-danger focus:!ring-danger' => $errors->has('description')])
                >{{ $old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-1" />
            </div>
        </x-card>

        <x-card class="p-5">
            <details class="group">
                <summary class="flex cursor-pointer list-none items-center justify-between font-serif text-sm font-semibold text-content">
                    SEO
                    <x-icon icon="chevron-down" class="h-4 w-4 text-muted transition-transform group-open:rotate-180" />
                </summary>

                <div class="mt-4 space-y-4">
                    <div>
                        <label for="meta_title" class="label">Meta title</label>
                        <input id="meta_title" type="text" name="meta_title" value="{{ $old('meta_title') }}" class="input-field">
                        <x-input-error :messages="$errors->get('meta_title')" class="mt-1" />
                    </div>

                    <div>
                        <label for="meta_description" class="label">Meta description</label>
                        <textarea id="meta_description" name="meta_description" rows="2" class="input-field resize-y">{{ $old('meta_description') }}</textarea>
                        <x-input-error :messages="$errors->get('meta_description')" class="mt-1" />
                    </div>
                </div>
            </details>
        </x-card>
    </div>

    <div class="space-y-6">
        <x-card class="space-y-5 p-5">
            <div>
                <label for="status" class="label">Status <span class="text-danger">*</span></label>
                <x-select id="status" name="status" :options="collect($statuses)->mapWithKeys(fn ($s) => [$s->value => $s->label()])->all()" :selected="$old('status', 'active')" required />
                <x-input-error :messages="$errors->get('status')" class="mt-1" />
            </div>

            <div>
                <label class="label">Image</label>
                <div class="flex items-center gap-3">
                    {{-- @if ($category?->image_url)
                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="h-16 w-24 shrink-0 rounded-xl object-cover shadow-soft">
                    @endif --}}
                    @if (!empty($category?->image_url))
                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="h-16 w-24 shrink-0 rounded-xl object-cover shadow-soft" >
                    @endif
                    <input type="file" name="image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" class="input-field cursor-pointer file:mr-3 file:rounded-lg file:border-0 file:bg-primary-soft file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-primary hover:file:bg-primary hover:file:text-primary-fg">
                </div>
                <x-input-error :messages="$errors->get('image')" class="mt-1" />
            </div>
        </x-card>
    </div>
</div>
