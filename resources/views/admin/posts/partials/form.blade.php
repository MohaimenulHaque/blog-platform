@php
    $old = fn (string $key, $default = null) => old($key, $post?->{$key} ?? $default);
    $selectedTags = old('tags', $post?->tags?->pluck('id')->all() ?? []);
@endphp

<div class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
        <x-card class="space-y-5 p-5">
            <div>
                <label for="title" class="label">Title <span class="text-danger">*</span></label>
                <input
                    id="title"
                    type="text"
                    name="title"
                    value="{{ $old('title') }}"
                    placeholder="e.g. The Quiet Art of Reading Slowly"
                    required
                    @class(['input-field', '!border-danger focus:!ring-danger' => $errors->has('title')])
                >
                <x-input-error :messages="$errors->get('title')" class="mt-1" />
            </div>

            <div>
                <label for="slug" class="label">Slug <span class="font-normal text-muted">(optional — auto-generated from title)</span></label>
                <input
                    id="slug"
                    type="text"
                    name="slug"
                    value="{{ $old('slug') }}"
                    placeholder="the-quiet-art-of-reading-slowly"
                    @class(['input-field', '!border-danger focus:!ring-danger' => $errors->has('slug')])
                >
                <x-input-error :messages="$errors->get('slug')" class="mt-1" />
            </div>

            <div>
                <label for="excerpt" class="label">Excerpt <span class="font-normal text-muted">(optional — auto-generated from content)</span></label>
                <textarea
                    id="excerpt"
                    name="excerpt"
                    rows="3"
                    placeholder="A short summary shown in post cards…"
                    @class(['input-field resize-y', '!border-danger focus:!ring-danger' => $errors->has('excerpt')])
                >{{ $old('excerpt') }}</textarea>
                <x-input-error :messages="$errors->get('excerpt')" class="mt-1" />
            </div>

            <div>
                <label for="content-editor" class="label">Content</label>
                <div @class(['!border-danger' => $errors->has('content')])>
                    @include('admin.posts.partials.editor', ['editorId' => 'content-editor', 'value' => $old('content')])
                </div>
                <x-input-error :messages="$errors->get('content')" class="mt-1" />
            </div>
        </x-card>
    </div>

    <div class="space-y-6">
        <x-card class="space-y-5 p-5">
            <div>
                <label for="status" class="label">Status <span class="text-danger">*</span></label>
                <x-select
                    id="status"
                    name="status"
                    :options="collect($statuses)->mapWithKeys(fn ($s) => [$s->value => $s->label()])->all()"
                    :selected="$old('status', 'draft')"
                    required
                />
                <x-input-error :messages="$errors->get('status')" class="mt-1" />
            </div>

            <div>
                <label for="visibility" class="label">Visibility <span class="text-danger">*</span></label>
                <x-select
                    id="visibility"
                    name="visibility"
                    :options="collect($visibilities)->mapWithKeys(fn ($v) => [$v->value => $v->label()])->all()"
                    :selected="$old('visibility', 'public')"
                    required
                />
                <x-input-error :messages="$errors->get('visibility')" class="mt-1" />
            </div>

            <div x-data>
                <label for="scheduled_at" class="label">Publish date <span class="font-normal text-muted">(for scheduled posts)</span></label>
                <input
                    id="scheduled_at"
                    type="datetime-local"
                    name="scheduled_at"
                    value="{{ $old('scheduled_at', $post?->scheduled_at?->format('Y-m-d\TH:i')) }}"
                    class="input-field"
                >
                <x-input-error :messages="$errors->get('scheduled_at')" class="mt-1" />
            </div>

            <div>
                <label for="category_id" class="label">Category</label>
                <x-select
                    id="category_id"
                    name="category_id"
                    :options="$categories->pluck('name', 'id')->all()"
                    :selected="$old('category_id')"
                    placeholder="No category"
                />
                <x-input-error :messages="$errors->get('category_id')" class="mt-1" />
            </div>
        </x-card>

        <x-card class="p-5">
            <div class="mb-4">
                <h2 class="font-serif text-sm font-semibold text-content">Images</h2>
                <p class="text-xs text-muted">JPEG, PNG, GIF or WebP up to 5 MB.</p>
            </div>

            <div class="space-y-4" x-data="{
                featured: '{{ $post?->featured_image_url ?? '' }}',
                featuredPath: '{{ $post?->featured_image ?? '' }}',
                pickerUrl: '{{ route('admin.media.index', ['picker' => 1]) }}',
                init() {
                    window.addEventListener('message', (e) => {
                        if (e.data?.type !== 'media-selected') {
                            return;
                        }
                        this.featured = e.data.url;
                        this.featuredPath = e.data.path;
                    });
                },
                previewFile(input) {
                    if (input.files && input.files[0]) {
                        this.featuredPath = '';
                        const reader = new FileReader();
                        reader.onload = e => this.featured = e.target.result;
                        reader.readAsDataURL(input.files[0]);
                    }
                }
            }">
                <div>
                    <label class="label">Featured image</label>
                    <div class="flex items-center gap-3">
                        <div class="ph-img grid h-20 w-28 shrink-0 place-items-center overflow-hidden rounded-xl border border-line text-3xl">
                            <template x-if="featured">
                                <img :src="featured" alt="Featured image preview" class="h-full w-full object-cover">
                            </template>
                            <span x-show="!featured" class="font-serif font-semibold text-primary">IMG</span>
                        </div>
                        <div class="flex-1 space-y-2">
                            <div class="flex flex-wrap gap-2">
                                <label class="btn btn-outline btn-sm cursor-pointer">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" /><circle cx="8.5" cy="8.5" r="1.5" /><path d="M21 15l-5-5L5 21" /></svg>
                                    Choose image
                                    <input type="file" name="featured_image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" class="hidden" @change="previewFile($event.target)">
                                </label>
                                <button type="button" class="btn btn-ghost btn-sm" @click="$dispatch('open-modal', 'media-picker')">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" /><circle cx="8.5" cy="8.5" r="1.5" /><path d="M21 15l-5-5L5 21" /></svg>
                                    Media library
                                </button>
                            </div>
                            <p class="text-xs text-muted">Shown at the top of the article.</p>
                            <p x-show="featuredPath" class="break-all text-xs text-primary" x-text="featuredPath"></p>
                        </div>
                    </div>
                    <input type="hidden" name="featured_image_path" x-model="featuredPath">
                    <x-input-error :messages="$errors->get('featured_image')" class="mt-1" />
                </div>

                <div>
                    <label class="label">Thumbnail</label>
                    <input
                        type="file"
                        name="thumbnail"
                        accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                        class="input-field cursor-pointer file:mr-3 file:rounded-lg file:border-0 file:bg-primary-soft file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-primary hover:file:bg-primary hover:file:text-primary-fg"
                    >
                    @if ($post?->thumbnail_url)
                        <p class="mt-1.5 text-xs text-muted">Current: <span class="break-all">{{ $post->thumbnail_url }}</span></p>
                    @endif
                    <x-input-error :messages="$errors->get('thumbnail')" class="mt-1" />
                </div>
            </div>
        </x-card>

        <x-card class="p-5">
            <h2 class="mb-3 font-serif text-sm font-semibold text-content">Tags</h2>

            @if ($tags->isEmpty())
                <p class="text-sm text-muted">No tags yet.</p>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach ($tags as $tag)
                        <label class="cursor-pointer select-none">
                            <input
                                type="checkbox"
                                name="tags[]"
                                value="{{ $tag->id }}"
                                class="peer sr-only"
                                @checked(in_array($tag->id, $selectedTags))
                            >
                            <span class="inline-flex items-center rounded-full border border-line-strong px-3 py-1.5 text-xs font-medium text-content-soft transition-colors peer-checked:border-primary peer-checked:bg-primary-soft peer-checked:text-primary">
                                {{ $tag->name }}
                            </span>
                        </label>
                    @endforeach
                </div>
            @endif

            <x-input-error :messages="$errors->get('tags')" class="mt-1" />
        </x-card>

        <x-card class="p-5">
            <details class="group">
                <summary class="flex cursor-pointer list-none items-center justify-between font-serif text-sm font-semibold text-content">
                    SEO &amp; Open Graph
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

                    <div>
                        <label for="meta_keywords" class="label">Meta keywords</label>
                        <input id="meta_keywords" type="text" name="meta_keywords" value="{{ $old('meta_keywords') }}" placeholder="comma, separated, keywords" class="input-field">
                        <x-input-error :messages="$errors->get('meta_keywords')" class="mt-1" />
                    </div>

                    <div>
                        <label for="canonical_url" class="label">Canonical URL</label>
                        <input id="canonical_url" type="url" name="canonical_url" value="{{ $old('canonical_url') }}" class="input-field">
                        <x-input-error :messages="$errors->get('canonical_url')" class="mt-1" />
                    </div>

                    <div>
                        <label for="og_title" class="label">OG title</label>
                        <input id="og_title" type="text" name="og_title" value="{{ $old('og_title') }}" class="input-field">
                        <x-input-error :messages="$errors->get('og_title')" class="mt-1" />
                    </div>

                    <div>
                        <label for="og_description" class="label">OG description</label>
                        <textarea id="og_description" name="og_description" rows="2" class="input-field resize-y">{{ $old('og_description') }}</textarea>
                        <x-input-error :messages="$errors->get('og_description')" class="mt-1" />
                    </div>
                </div>
            </details>
        </x-card>
    </div>
</div>

<x-modal name="media-picker" maxWidth="5xl" focusable>
    <div class="flex h-full max-h-[80vh] flex-col">
        <div class="flex items-center justify-between border-b border-line px-6 py-4">
            <div>
                <h2 class="font-serif text-lg font-semibold text-content">Media library</h2>
                <p class="text-sm text-muted">Select an image to use as the featured image.</p>
            </div>
            <button type="button" class="grid h-9 w-9 place-items-center rounded-xl text-muted transition-colors hover:bg-surface-alt hover:text-content" x-data x-on:click="$dispatch('close')" aria-label="Close media picker">
                <x-icon icon="close" class="h-5 w-5" />
            </button>
        </div>
        <div class="flex-1 overflow-hidden">
            <iframe src="{{ route('admin.media.index', ['picker' => 1]) }}" class="h-full w-full" title="Media library" frameborder="0"></iframe>
        </div>
    </div>
</x-modal>
