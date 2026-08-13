<x-app-layout>
    <x-slot name="title">{{ __('Tags') }}</x-slot>
    <x-slot name="metaDescription">{{ __('Explore every topic tag used across the blog and discover articles by theme.') }}</x-slot>

    @push('jsonld')
        <x-json-ld :data="[
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => __('Tags'),
            'description' => __('Browse every topic discussed on the blog.'),
            'url' => route('tags.index'),
            'inLanguage' => app()->getLocale(),
        ]" />
    @endpush

    <x-page-header
        eyebrow="Index"
        :title="__('Tags')"
        :description="__('A cloud of labels used across the blog. Follow a tag to see every post carrying it.')"
    />

    <section>
        <x-container class="py-16 md:py-20">
            @if ($tags->isEmpty())
                <x-empty-state
                    icon="tags"
                    title="No tags yet"
                    description="Tags will appear here once posts start using them."
                />
            @else
                <div class="flex flex-wrap justify-center gap-3">
                    @foreach ($tags as $tag)
                        <a
                            href="{{ $tag->url }}"
                            class="group inline-flex items-center gap-2 rounded-full border border-line bg-surface px-4 py-2 text-sm font-medium text-content-soft transition-all duration-200 hover:border-primary hover:bg-primary-soft hover:text-primary"
                        >
                            <span class="text-primary">#</span>{{ $tag->name }}
                            <span class="text-xs text-muted transition-colors group-hover:text-primary">· {{ $tag->posts_count }}</span>
                        </a>
                    @endforeach
                </div>

                @if ($tags->hasPages())
                    <div class="mt-12">
                        <x-pagination :paginator="$tags" />
                    </div>
                @endif
            @endif
        </x-container>
    </section>
</x-app-layout>
