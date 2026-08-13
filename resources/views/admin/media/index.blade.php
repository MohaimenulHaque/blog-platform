<x-admin-layout>
    <x-slot name="title">{{ __('Media library') }}</x-slot>

    <div x-data="mediaPicker('{{ $picker ? 1 : 0 }}')" class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">Media library</h1>
                <p class="mt-0.5 text-sm text-muted">Upload and manage images used across the site.</p>
            </div>

            @if (! $picker)
                <div class="flex flex-wrap items-center gap-2">
                    <x-button variant="primary" size="md" type="button" x-data x-on:click="$dispatch('open-modal', 'upload-media')">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" /><polyline points="17 8 12 3 7 8" /><line x1="12" y1="3" x2="12" y2="15" /></svg>
                        Upload
                    </x-button>
                </div>
            @endif
        </div>

        @if (session('status'))
            <x-alert type="success" :dismissible="true">{{ session('status') }}</x-alert>
        @endif

        @if (session('error'))
            <x-alert type="danger" :dismissible="true">{{ session('error') }}</x-alert>
        @endif

        <x-card>
            <form method="GET" action="{{ route('admin.media.index') }}" class="flex flex-col gap-3 border-b border-line p-4 sm:flex-row sm:items-center">
                @if ($picker)
                    <input type="hidden" name="picker" value="1">
                @endif
                <div class="relative flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-muted">
                        <x-icon icon="search" class="h-4 w-4" />
                    </span>
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Search media…"
                        class="input-field pl-10"
                    >
                </div>

                <div class="flex gap-2">
                    <x-button variant="primary" size="md" type="submit">Search</x-button>
                    @if ($search)
                        <x-button variant="ghost" size="md" :href="$picker
                            ? route('admin.media.index', ['picker' => 1])
                            : route('admin.media.index')">Reset</x-button>
                    @endif
                </div>
            </form>

            @if ($media->isEmpty())
                <x-empty-state
                    icon="images"
                    title="No media yet"
                    :description="$search
                        ? 'No images match your search.'
                        : 'Upload your first image to start building the library.'"
                >
                    @if (! $picker)
                        <x-slot name="action">
                            <x-button variant="primary" type="button" x-data x-on:click="$dispatch('open-modal', 'upload-media')">Upload images</x-button>
                        </x-slot>
                    @endif
                </x-empty-state>
            @else
                <ul class="grid grid-cols-2 gap-4 p-4 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6">
                    @foreach ($media as $item)
                        <li>
                            <button
                                type="button"
                                @if ($picker)
                                    @click="select('{{ $item->url }}', '{{ $item->path }}', '{{ addslashes($item->name) }}')"
                                    class="group relative block w-full overflow-hidden rounded-xl border-2 transition-all"
                                    :class="selectedPath === '{{ $item->path }}' ? 'border-primary ring-4 ring-primary-ring' : 'border-line hover:border-line-strong'"
                                @else
                                    x-data
                                    x-on:click="$dispatch('open-modal', 'preview-media-{{ $item->id }}')"
                                    class="group relative block w-full overflow-hidden rounded-xl border border-line transition-all hover:border-line-strong hover:shadow-lift"
                                @endif
                                aria-label="{{ $item->name }}"
                            >
                                <img
                                    src="{{ $item->url }}"
                                    alt="{{ $item->alt_text ?? $item->name }}"
                                    loading="lazy"
                                    class="aspect-square w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                >
                                <span class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent px-2 pb-1.5 pt-6 text-left text-[11px] font-medium text-white opacity-0 transition-opacity group-hover:opacity-100">
                                    {{ $item->name }}
                                </span>
                            </button>

                            @if (! $picker)
                                <x-modal name="preview-media-{{ $item->id }}" maxWidth="lg" focusable>
                                    <div class="p-6">
                                        <div class="ph-img grid max-h-96 place-items-center overflow-hidden rounded-xl border border-line">
                                            <img src="{{ $item->url }}" alt="{{ $item->alt_text ?? $item->name }}" class="max-h-96 w-full object-contain">
                                        </div>

                                        <div class="mt-5">
                                            <h2 class="font-serif text-lg font-semibold text-content">{{ $item->name }}</h2>
                                            <p class="mt-1 text-sm text-muted">
                                                {{ $item->original_name }} · {{ number_format($item->size / 1024, 1) }} KB · {{ $item->extension }}
                                                @if ($item->width) · {{ $item->width }}×{{ $item->height }}px @endif
                                            </p>
                                            <p class="mt-1 break-all text-xs text-muted">{{ $item->url }}</p>
                                        </div>

                                        <form method="POST" action="{{ route('admin.media.update', $item) }}" class="mt-4 space-y-3">
                                            @csrf
                                            @method('PATCH')
                                            <div>
                                                <label for="media_name_{{ $item->id }}" class="label">Name</label>
                                                <input id="media_name_{{ $item->id }}" type="text" name="name" value="{{ $item->name }}" class="input-field">
                                            </div>
                                            <div>
                                                <label for="media_alt_{{ $item->id }}" class="label">Alt text</label>
                                                <input id="media_alt_{{ $item->id }}" type="text" name="alt_text" value="{{ $item->alt_text }}" placeholder="Describe the image for screen readers" class="input-field">
                                            </div>
                                            <div class="flex items-center justify-between gap-3">
                                                <form method="POST" action="{{ route('admin.media.destroy', $item) }}" onsubmit="return confirm('Delete this image permanently?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-button variant="danger" size="sm" type="submit">Delete</x-button>
                                                </form>
                                                <div class="flex gap-2">
                                                    <x-button variant="ghost" size="sm" type="button" x-data x-on:click="$dispatch('close')">Close</x-button>
                                                    <x-button variant="primary" size="sm" type="submit">Save</x-button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </x-modal>
                            @endif
                        </li>
                    @endforeach
                </ul>

                <div class="border-t border-line p-4">
                    <x-pagination :paginator="$media" />
                </div>
            @endif
        </x-card>

        @if ($picker)
            <div class="sticky bottom-4 z-10 flex items-center justify-between gap-3 rounded-2xl border border-line bg-surface p-3 shadow-lift" x-show="selectedPath" x-cloak>
                <div class="flex min-w-0 items-center gap-3">
                    <img :src="selectedUrl" :alt="selectedName" class="h-12 w-16 shrink-0 rounded-lg object-cover">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-content" x-text="selectedName"></p>
                        <p class="truncate text-xs text-muted" x-text="selectedPath"></p>
                    </div>
                </div>
                <div class="flex shrink-0 gap-2">
                    <x-button variant="ghost" size="sm" type="button" @click="clearSelection()">Clear</x-button>
                    <x-button variant="primary" size="sm" type="button" @click="useSelected()">Use selected image</x-button>
                </div>
            </div>
        @endif

        @if (! $picker)
            <x-modal name="upload-media" maxWidth="md" focusable>
                <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="p-6">
                    @csrf
                    <h2 class="font-serif text-lg font-semibold text-content">Upload images</h2>
                    <p class="mt-1 text-sm text-muted">JPEG, PNG, GIF or WebP up to 5 MB each. Up to 10 at a time.</p>

                    <label class="mt-5 flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-line-strong bg-surface-alt/50 px-6 py-10 text-center transition-colors hover:border-primary hover:bg-primary-soft/50">
                        <span class="grid h-12 w-12 place-items-center rounded-xl bg-primary-soft text-primary">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" /><polyline points="17 8 12 3 7 8" /><line x1="12" y1="3" x2="12" y2="15" /></svg>
                        </span>
                        <span class="text-sm font-semibold text-content">Click to choose images</span>
                        <span class="text-xs text-muted">or drag and drop files here</span>
                        <input type="file" name="files[]" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" multiple class="hidden" required>
                    </label>
                    <x-input-error :messages="$errors->get('files')" class="mt-2" />

                    <div class="mt-6 flex justify-end gap-3">
                        <x-button variant="ghost" size="md" type="button" x-data x-on:click="$dispatch('close')">Cancel</x-button>
                        <x-button variant="primary" size="md" type="submit">Upload</x-button>
                    </div>
                </form>
            </x-modal>
        @endif
    </div>
</x-admin-layout>
