<x-app-layout>
    <x-slot name="title">{{ $search ? 'Search: '.$search : __('The Blog') }}</x-slot>
    <x-slot name="metaDescription">{{ __('Thoughtful essays, guides and interviews — published weekly by our editorial team.') }}</x-slot>

    @push('jsonld')
        <x-json-ld :data="[
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => __('The Blog'),
            'description' => __('Thoughtful essays, guides and interviews — published weekly by our editorial team.'),
            'url' => route('blog.index'),
            'inLanguage' => app()->getLocale(),
        ]" />
        <x-json-ld :data="[
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => route('blog.index')],
            ],
        ]" />
    @endpush

    <x-page-header
        eyebrow="Journal"
        :title="__('The Blog')"
        :description="__('Thoughtful essays, guides and interviews — published weekly by our editorial team.')"
    />

    <section>
        <x-container class="py-12 md:py-16">
            @php
                $categoryOptions = $categories->pluck('name', 'slug')->toArray();
                $tagOptions = $tags->mapWithKeys(fn ($t) => [$t->slug => $t->name.' ('.$t->posts_count.')'])->toArray();
                $authorOptions = $authors->pluck('name', 'username')->toArray();
            @endphp

            {{-- Search + filters --}}
            <form method="GET" action="{{ route('blog.index') }}" class="space-y-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                    <div class="relative flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-muted">
                            <x-icon icon="search" class="h-5 w-5" />
                        </span>
                        <label for="blog-search" class="sr-only">Search posts</label>
                        <input
                            id="blog-search"
                            type="search"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Search posts…"
                            class="input-field py-3 pl-12"
                        >
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <x-select name="category" :options="$categoryOptions" :selected="$filters['category']" emptyOption="All categories" wrapperClass="lg:w-44" />
                        <x-select name="tag" :options="$tagOptions" :selected="$filters['tag']" emptyOption="All tags" wrapperClass="lg:w-44" />
                        <x-select name="author" :options="$authorOptions" :selected="$filters['author']" emptyOption="All authors" wrapperClass="lg:w-44" />
                        <x-select name="sort" :options="['latest' => 'Latest', 'oldest' => 'Oldest', 'popular' => 'Most popular', 'title' => 'A–Z']" :selected="$filters['sort']" emptyOption="Sort" wrapperClass="lg:w-44" />
                    </div>

                    <div class="flex items-center gap-2">
                        <x-button type="submit" variant="primary">Apply</x-button>
                        @if ($search || $filters['category'] || $filters['tag'] || $filters['author'] || $filters['sort'] !== 'latest')
                            <a href="{{ route('blog.index') }}" class="btn btn-ghost btn-md">
                                <x-icon icon="close" class="h-4 w-4" />
                                Clear
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <p class="mt-6 text-sm text-muted" aria-live="polite">
                @if ($search)
                    <strong class="text-content">{{ $posts->total() }}</strong> {{ Str::plural('result', $posts->total()) }} for “<strong class="text-content">{{ $search }}</strong>”
                @else
                    <strong class="text-content">{{ $posts->total() }}</strong> {{ Str::plural('post', $posts->total()) }} published
                @endif
            </p>

            <div class="mt-6">
                @if ($posts->isEmpty())
                    <x-empty-state
                        icon="search"
                        title="No posts found"
                        description="Try adjusting your search or filters — or clear them to see everything."
                    >
                        <x-slot name="action">
                            <a href="{{ route('blog.index') }}" class="btn btn-primary btn-md">
                                Clear filters
                            </a>
                        </x-slot>
                    </x-empty-state>
                @else
                    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($posts as $post)
                            @if ($loop->first && $posts->currentPage() === 1)
                                <article class="card card-hover group relative col-span-full flex flex-col overflow-hidden md:flex-row">
                                    <div class="relative aspect-[16/10] overflow-hidden md:w-1/2 md:aspect-auto {{ $post->featured_image_url ? '' : 'ph-img' }}">
                                        @if ($post->featured_image_url)
                                            <img
                                                src="{{ $post->featured_image_url }}"
                                                alt="{{ $post->title }}"
                                                loading="lazy"
                                                decoding="async"
                                                class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                                            >
                                        @else
                                            <div class="absolute inset-0 flex items-center justify-center text-primary/20 transition-transform duration-500 group-hover:scale-110">
                                                <x-icon icon="pen" class="h-16 w-16" />
                                            </div>
                                        @endif
                                        <x-badge variant="primary" class="absolute left-4 top-4 z-10 shadow-soft">{{ $post->category?->name ?? 'Uncategorized' }}</x-badge>
                                        <x-badge variant="secondary" class="absolute right-4 top-4 z-10 shadow-soft">
                                            <x-icon icon="sparkles" class="h-3 w-3" />
                                            Featured
                                        </x-badge>
                                    </div>

                                    <div class="flex flex-1 flex-col justify-center p-6 md:p-8">
                                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-muted">
                                            <span class="inline-flex items-center gap-1.5"><x-icon icon="calendar" class="h-3.5 w-3.5" /> {{ $post->published_at?->format('M j, Y') }}</span>
                                            <span aria-hidden="true">·</span>
                                            <span class="inline-flex items-center gap-1.5"><x-icon icon="clock" class="h-3.5 w-3.5" /> {{ $post->reading_time }} min read</span>
                                            <span aria-hidden="true">·</span>
                                            <span class="inline-flex items-center gap-1.5">
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" /></svg>
                                                {{ number_format($post->view_count) }} views
                                            </span>
                                        </div>

                                        <h2 class="mt-3 font-serif text-2xl font-semibold leading-snug tracking-tight text-content transition-colors group-hover:text-primary md:text-3xl">
                                            <a href="{{ $post->url }}" class="focus-visible:outline-none">
                                                <span class="absolute inset-0" aria-hidden="true"></span>
                                                {{ $post->title }}
                                            </a>
                                        </h2>

                                        <p class="mt-3 max-w-xl text-sm leading-relaxed text-muted text-pretty md:text-base">{{ $post->excerpt }}</p>

                                        <div class="mt-6 flex items-center gap-3 border-t border-line pt-5">
                                            <x-avatar :user="$post->author" size="md" />
                                            <div class="text-sm">
                                                <a href="{{ $post->author?->author_url }}" class="font-semibold text-content transition-colors hover:text-primary">{{ $post->author?->name }}</a>
                                                <p class="text-xs text-muted">{{ $post->author?->designation_label }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @else
                                <x-post-card
                                    :title="$post->title"
                                    :excerpt="$post->excerpt"
                                    :category="$post->category?->name ?? 'Uncategorized'"
                                    :date="$post->published_at?->format('M j, Y')"
                                    :readTime="$post->reading_time.' min read'"
                                    :author="$post->author?->name"
                                    :user="$post->author"
                                    :authorHref="$post->author?->author_url"
                                    :views="$post->view_count"
                                    :href="$post->url"
                                    :image="$post->featured_image_url"
                                />
                            @endif
                        @endforeach
                    </div>

                    @if ($posts->hasPages())
                        <div class="mt-12">
                            <x-pagination :paginator="$posts" />
                        </div>
                    @endif
                @endif
            </div>
        </x-container>
    </section>
</x-app-layout>
