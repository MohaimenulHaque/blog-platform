<x-app-layout>
    <x-slot name="title">{{ __('Bookmarks') }}</x-slot>
    <x-slot name="noindex">true</x-slot>
    <x-slot name="metaDescription">{{ __('Posts you have saved to read later.') }}</x-slot>

    <x-page-header
        eyebrow="Library"
        :title="__('Your bookmarks')"
        :description="__('Posts you have saved to read later.')"
    />

    <section>
        <x-container class="py-12 md:py-16">
            @if ($bookmarks->isEmpty())
                <div class="mx-auto max-w-md text-center">
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-surface-alt text-muted">
                        <x-icon icon="bookmark" class="h-7 w-7" />
                    </div>
                    <h2 class="mt-5 font-serif text-xl font-semibold text-content">No bookmarks yet</h2>
                    <p class="mt-2 text-sm leading-relaxed text-muted">
                        When you save a post, it will show up here so you can find it later.
                    </p>
                    <a href="{{ route('blog.index') }}" class="btn btn-primary btn-md mt-6">
                        Browse the blog
                        <x-icon icon="arrow-right" class="h-4 w-4" />
                    </a>
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($bookmarks as $bookmark)
                        <x-post-card
                            :title="$bookmark->post->title"
                            :excerpt="$bookmark->post->excerpt"
                            :category="$bookmark->post->category?->name ?? 'Uncategorized'"
                            :date="$bookmark->post->published_at?->format('M j, Y')"
                            :readTime="$bookmark->post->reading_time.' min read'"
                            :author="$bookmark->post->author?->name"
                            :user="$bookmark->post->author"
                            :authorHref="$bookmark->post->author?->author_url"
                            :views="$bookmark->post->view_count"
                            :href="$bookmark->post->url"
                            :image="$bookmark->post->featured_image_url"
                        />
                    @endforeach
                </div>

                <div class="mt-10">
                    <x-pagination :paginator="$bookmarks" />
                </div>
            @endif
        </x-container>
    </section>
</x-app-layout>
