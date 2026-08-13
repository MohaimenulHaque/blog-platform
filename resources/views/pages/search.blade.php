<x-app-layout>
    <x-slot name="title">{{ $query ? 'Search: '.$query : __('Search') }}</x-slot>
    <x-slot name="metaDescription">{{ __('Search the full archive — by title, excerpt, category, tag or author.') }}</x-slot>
    <x-slot name="canonical">{{ $query === '' ? route('search') : route('search', ['q' => $query]) }}</x-slot>

    @push('head')
        <meta name="robots" content="noindex, follow">
    @endpush

    <x-page-header
        eyebrow="Search"
        :title="__('Find a story')"
        :description="__('Search the full archive — articles, categories, tags and authors.')"
    />

    <section>
        <x-container class="py-14 md:py-20">
            <form class="mx-auto max-w-2xl" method="GET" action="{{ route('search') }}">
                <label for="search-query" class="sr-only">Search query</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-muted">
                        <x-icon icon="search" class="h-5 w-5" />
                    </span>
                    <input
                        id="search-query"
                        type="search"
                        name="q"
                        value="{{ $query }}"
                        placeholder="Search articles, authors, topics…"
                        autocomplete="off"
                        autofocus
                        class="input-field py-3.5 pl-12"
                    >
                </div>
                <div class="mt-3 flex justify-end">
                    <x-button type="submit" variant="primary">Search</x-button>
                </div>
            </form>

            @if ($query === '')
                {{-- Suggestions --}}
                <div class="mx-auto mt-12 max-w-3xl">
                    <div class="card p-8 text-center">
                        <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-surface-alt text-muted">
                            <x-icon icon="search" class="h-7 w-7" />
                        </div>
                        <h2 class="mt-4 font-serif text-xl font-semibold text-content">Type to search the archive</h2>
                        <p class="mt-1 text-sm text-muted text-pretty">
                            We'll look across titles, excerpts, content, categories, tags and author names.
                        </p>
                    </div>

                    @if ($popularTags->isNotEmpty())
                        <div class="mt-10 text-center">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">Popular topics</p>
                            <div class="mt-4 flex flex-wrap justify-center gap-2">
                                @foreach ($popularTags as $tag)
                                    <a href="{{ route('tags.show', $tag->slug) }}" class="inline-flex items-center rounded-full bg-surface-alt px-3.5 py-1.5 text-sm font-medium text-content-soft transition-colors hover:bg-primary-soft hover:text-primary">
                                        #{{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($popularCategories->isNotEmpty())
                        <div class="mt-8 text-center">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">Browse categories</p>
                            <div class="mt-4 flex flex-wrap justify-center gap-2">
                                @foreach ($popularCategories as $category)
                                    <a href="{{ $category->url }}" class="inline-flex items-center rounded-full border border-line bg-surface px-3.5 py-1.5 text-sm font-medium text-content-soft transition-colors hover:border-primary hover:bg-primary-soft hover:text-primary">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @elseif ($posts && $posts->isEmpty())
                <div class="mx-auto mt-12 max-w-xl">
                    <x-empty-state
                        icon="search"
                        title="No results found"
                        :description="'Nothing matched “'.$query.'”. Try a different term or browse the archive.'"
                    >
                        <x-slot name="action">
                            <x-button variant="outline" href="{{ route('blog.index') }}">Browse all posts</x-button>
                        </x-slot>
                    </x-empty-state>
                </div>
            @elseif ($posts)
                <p class="mx-auto mt-10 max-w-2xl text-sm text-muted" aria-live="polite">
                    <strong class="text-content">{{ $posts->total() }}</strong> {{ Str::plural('result', $posts->total()) }} for “<strong class="text-content">{{ $query }}</strong>”
                </p>

                <div class="mx-auto mt-6 grid max-w-2xl gap-6">
                    @foreach ($posts as $post)
                        <a href="{{ $post->url }}" class="card card-hover group flex flex-col gap-4 overflow-hidden p-5 sm:flex-row sm:items-center sm:p-0">
                            <div class="relative h-40 shrink-0 overflow-hidden sm:h-full sm:w-52 {{ $post->featured_image_url ? '' : 'ph-img' }}">
                                @if ($post->featured_image_url)
                                    <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center text-primary/20">
                                        <x-icon icon="pen" class="h-10 w-10" />
                                    </div>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1 p-0 sm:p-5">
                                <div class="flex flex-wrap items-center gap-2 text-xs text-muted">
                                    <x-badge variant="primary">{{ $post->category?->name ?? 'Uncategorized' }}</x-badge>
                                    <span>{{ $post->published_at?->format('M j, Y') }}</span>
                                </div>
                                <h2 class="mt-2 font-serif text-lg font-semibold leading-snug text-content transition-colors group-hover:text-primary">{{ $post->title }}</h2>
                                <p class="mt-1.5 text-sm leading-relaxed text-muted line-clamp-2">{{ $post->excerpt }}</p>
                                <div class="mt-3 flex items-center gap-2.5">
                                    <x-avatar :user="$post->author" size="sm" />
                                    <span class="text-sm font-medium text-content-soft">{{ $post->author?->name }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if ($posts->hasPages())
                    <div class="mt-12">
                        <x-pagination :paginator="$posts" />
                    </div>
                @endif
            @endif
        </x-container>
    </section>
</x-app-layout>
