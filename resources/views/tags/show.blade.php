<x-app-layout>
    <x-slot name="title">{{ '#'.$tag->name }}</x-slot>
    <x-slot name="metaDescription">{{ 'Every post tagged '.$tag->name.' on '.config('app.name').'.' }}</x-slot>
    <x-slot name="canonical">{{ $tag->url }}</x-slot>

    @push('jsonld')
        <x-json-ld :data="[
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => '#'.$tag->name,
            'description' => 'Every post tagged '.$tag->name.'.',
            'url' => $tag->url,
            'inLanguage' => app()->getLocale(),
        ]" />
        <x-json-ld :data="[
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Tags', 'item' => route('tags.index')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => '#'.$tag->name, 'item' => $tag->url],
            ],
        ]" />
    @endpush

    <x-page-header
        eyebrow="Tag"
        :title="'#'.$tag->name"
        :description="'Every post tagged '.$tag->name.'.'"
    />

    <section>
        <x-container class="py-16 md:py-20">
            <div class="mb-8 flex items-center justify-between">
                <p class="text-sm text-muted">
                    <strong class="text-content">{{ $posts->total() }}</strong> {{ Str::plural('post', $posts->total()) }} tagged with
                    <span class="inline-flex items-center rounded-full bg-primary-soft px-2.5 py-0.5 text-sm font-semibold text-primary">#{{ $tag->name }}</span>
                </p>
                <a href="{{ route('tags.index') }}" class="hidden text-sm font-semibold text-primary hover:underline sm:inline">All tags →</a>
            </div>

            @if ($posts->isEmpty())
                <x-empty-state
                    icon="documents"
                    :title="'No posts tagged '.$tag->name.' yet'"
                    description="Posts using this tag will appear here as they are published."
                >
                    <x-slot name="action">
                        <x-button variant="outline" href="{{ route('tags.index') }}">Browse all tags</x-button>
                    </x-slot>
                </x-empty-state>
            @else
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($posts as $post)
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
