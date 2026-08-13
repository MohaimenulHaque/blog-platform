<x-app-layout>
    <x-slot name="title">{{ $category->name }}</x-slot>
    <x-slot name="metaDescription">{{ $category->meta_description ?? $category->description }}</x-slot>
    <x-slot name="canonical">{{ $category->url }}</x-slot>

    @push('jsonld')
        <x-json-ld :data="[
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $category->name,
            'description' => $category->meta_description ?? $category->description,
            'url' => $category->url,
            'inLanguage' => app()->getLocale(),
        ]" />
        <x-json-ld :data="[
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Categories', 'item' => route('categories.index')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $category->name, 'item' => $category->url],
            ],
        ]" />
    @endpush

    <header class="relative overflow-hidden border-b border-line bg-surface">
        @if ($category->image_url)
            <div class="absolute inset-0" aria-hidden="true">
                <img src="{{ $category->image_url }}" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover opacity-15">
                <div class="absolute inset-0 bg-gradient-to-b from-transparent to-surface"></div>
            </div>
        @endif

        <x-container class="relative py-14 md:py-20">
            <nav class="text-sm text-muted" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('home') }}" class="transition-colors hover:text-primary">Home</a></li>
                    <li aria-hidden="true"><x-icon icon="chevron-down" class="h-3.5 w-3.5 -rotate-90" /></li>
                    <li><a href="{{ route('categories.index') }}" class="transition-colors hover:text-primary">Categories</a></li>
                    <li aria-hidden="true"><x-icon icon="chevron-down" class="h-3.5 w-3.5 -rotate-90" /></li>
                    <li aria-current="page" class="font-medium text-content">{{ $category->name }}</li>
                </ol>
            </nav>

            <div class="mt-5 max-w-3xl">
                <p class="eyebrow">
                    <span class="h-px w-6 bg-current"></span>
                    Category · {{ trans_choice(':count post|:count posts', $category->posts_count) }}
                </p>

                <h1 class="mt-3 font-serif text-4xl font-semibold tracking-tight text-content text-balance md:text-5xl">{{ $category->name }}</h1>

                @if ($category->description)
                    <p class="mt-4 max-w-2xl text-lg leading-relaxed text-muted text-pretty">{{ $category->description }}</p>
                @endif
            </div>
        </x-container>
    </header>

    <section>
        <x-container class="py-16 md:py-20">
            @if ($posts->isEmpty())
                <x-empty-state
                    icon="documents"
                    :title="'Nothing in '.$category->name.' yet'"
                    description="Posts in this category will appear here as they are published."
                >
                    <x-slot name="action">
                        <x-button variant="outline" href="{{ route('categories.index') }}">Browse all categories</x-button>
                    </x-slot>
                </x-empty-state>
            @else
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($posts as $post)
                        <x-post-card
                            :title="$post->title"
                            :excerpt="$post->excerpt"
                            :category="$category->name"
                            :date="$post->published_at?->format('M j, Y')"
                            :readTime="$post->reading_time.' min read'"
                            :author="$post->author?->name"
                            :user="$post->author"
                            :authorHref="$post->author?->author_url"
                            :views="$post->view_count"
                            :href="$post->url"
                            :image="$post->featured_image_url"
                        />
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

    <section class="border-t border-line bg-surface">
        <x-container class="py-14 md:py-16">
            <div class="flex items-end justify-between">
                <div>
                    <p class="eyebrow">Explore</p>
                    <h2 class="mt-2 font-serif text-2xl font-semibold tracking-tight text-content">More categories</h2>
                </div>
                <a href="{{ route('categories.index') }}" class="hidden text-sm font-semibold text-primary hover:underline sm:inline">All categories →</a>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                @foreach ($relatedCategories as $related)
                    <a href="{{ $related->url }}" class="inline-flex items-center gap-2 rounded-full border border-line bg-surface px-4 py-2 text-sm font-medium text-content-soft transition-colors hover:border-primary hover:bg-primary-soft hover:text-primary">
                        {{ $related->name }}
                        <span class="text-xs text-muted">{{ trans_choice(':count|:count', $related->posts_count) }}</span>
                    </a>
                @endforeach
            </div>
        </x-container>
    </section>
</x-app-layout>
