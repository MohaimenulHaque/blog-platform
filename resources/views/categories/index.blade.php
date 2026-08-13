<x-app-layout>
    <x-slot name="title">{{ __('Categories') }}</x-slot>
    <x-slot name="metaDescription">{{ __('Browse all article categories on the blog and find the topics that interest you most.') }}</x-slot>

    @push('jsonld')
        <x-json-ld :data="[
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => __('Categories'),
            'description' => __('Every post on the blog, organised into topics. Pick a category to dive in.'),
            'url' => route('categories.index'),
            'inLanguage' => app()->getLocale(),
        ]" />
    @endpush

    <x-page-header
        eyebrow="Browse"
        :title="__('Categories')"
        :description="__('Every post on the blog, organised into topics. Pick a category to dive in.')"
    />

    <section>
        <x-container class="py-16 md:py-20">
            @if ($categories->isEmpty())
                <x-empty-state
                    icon="folders"
                    title="No categories yet"
                    description="Categories will appear here once our editors set them up."
                />
            @else
                <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($categories as $category)
                        <x-category-card
                            :name="$category->name"
                            :description="$category->description"
                            :count="trans_choice(':count article|:count articles', $category->posts_count)"
                            :href="$category->url"
                            :image="$category->image_url"
                        />
                    @endforeach
                </div>

                @if ($categories->hasPages())
                    <div class="mt-12">
                        <x-pagination :paginator="$categories" />
                    </div>
                @endif
            @endif
        </x-container>
    </section>
</x-app-layout>
