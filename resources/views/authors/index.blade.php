<x-app-layout>
    <x-slot name="title">{{ __('Authors') }}</x-slot>
    <x-slot name="metaDescription">{{ __('Meet the writers behind the blog and explore their published articles.') }}</x-slot>

    @push('jsonld')
        <x-json-ld :data="[
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => __('Authors'),
            'description' => __('Meet the writers behind the blog.'),
            'url' => route('authors.index'),
            'inLanguage' => app()->getLocale(),
        ]" />
    @endpush

    <x-page-header
        eyebrow="The Team"
        :title="__('Authors')"
        :description="__('The writers and editors behind every post published on the blog.')"
    />

    <section>
        <x-container class="py-16 md:py-20">
            @if ($authors->isEmpty())
                <x-empty-state
                    icon="users"
                    title="No authors yet"
                    description="The team page will fill up as authors join the blog."
                />
            @else
                <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($authors as $author)
                        <x-author-card
                            :name="$author->name"
                            :role="$author->designation_label"
                            :bio="$author->bio"
                            :user="$author"
                            :href="route('authors.show', $author->username)"
                        />
                    @endforeach
                </div>

                @if ($authors->hasPages())
                    <div class="mt-12">
                        <x-pagination :paginator="$authors" />
                    </div>
                @endif
            @endif
        </x-container>
    </section>
</x-app-layout>
