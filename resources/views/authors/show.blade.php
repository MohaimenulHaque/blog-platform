<x-app-layout>
    <x-slot name="title">{{ $author->name }}</x-slot>
    <x-slot name="metaDescription">{{ $author->bio ?? ($author->name.' — writer on '.config('app.name').'.') }}</x-slot>
    <x-slot name="canonical">{{ $author->author_url }}</x-slot>

    @push('jsonld')
        <x-json-ld :data="[
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            '@id' => $author->author_url,
            'name' => $author->name,
            'url' => $author->author_url,
            'jobTitle' => $author->designation_label,
            'description' => $author->bio,
            'image' => $author->avatar_url,
            'sameAs' => array_values($author->social_links),
        ]" />
        <x-json-ld :data="[
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Authors', 'item' => route('authors.index')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $author->name, 'item' => $author->author_url],
            ],
        ]" />
    @endpush

    <section class="border-b border-line bg-surface">
        <x-container class="py-14 md:py-20">
            <div class="mx-auto max-w-3xl text-center">
                <x-avatar :user="$author" size="2xl" class="mx-auto ring-8 ring-line/50" />

                <h1 class="mt-6 font-serif text-4xl font-semibold tracking-tight text-content">{{ $author->name }}</h1>
                <p class="mt-2 text-sm font-semibold uppercase tracking-[0.18em] text-primary">{{ $author->designation_label }}</p>

                @if ($author->bio)
                    <p class="mx-auto mt-5 max-w-xl text-lg leading-relaxed text-muted text-pretty">{{ $author->bio }}</p>
                @endif

                <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-surface-alt px-4 py-1.5 text-sm font-medium text-content-soft">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z" /></svg>
                        {{ trans_choice(':count published post|:count published posts', $stats['posts']) }}
                    </span>

                    @if ($author->website)
                        <a href="{{ $author->website }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 rounded-full bg-surface-alt px-4 py-1.5 text-sm font-medium text-content-soft transition-colors hover:bg-primary-soft hover:text-primary">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10" /><line x1="2" y1="12" x2="22" y2="12" /><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" /></svg>
                            Website
                        </a>
                    @endif
                </div>

                @if ($author->social_links)
                    <div class="mt-6 flex items-center justify-center gap-3">
                        @foreach ($author->social_links as $platform => $url)
                            <x-social-icon
                                :url="$url"
                                :label="$author->name.' on '.ucfirst($platform)"
                                :icon="$platform"
                            />
                        @endforeach
                    </div>
                @endif
            </div>
        </x-container>
    </section>

    {{-- Statistics --}}
    <section class="border-b border-line">
        <x-container class="py-10">
            <dl class="grid grid-cols-2 gap-6 lg:grid-cols-4">
                <div class="card flex flex-col items-center gap-1 p-6 text-center">
                    <dt class="text-xs font-medium uppercase tracking-wider text-muted">Posts</dt>
                    <dd class="font-serif text-3xl font-semibold text-content">{{ number_format($stats['posts']) }}</dd>
                </div>
                <div class="card flex flex-col items-center gap-1 p-6 text-center">
                    <dt class="text-xs font-medium uppercase tracking-wider text-muted">Total reads</dt>
                    <dd class="font-serif text-3xl font-semibold text-content">{{ number_format($stats['views']) }}</dd>
                </div>
                <div class="card flex flex-col items-center gap-1 p-6 text-center">
                    <dt class="text-xs font-medium uppercase tracking-wider text-muted">Likes</dt>
                    <dd class="font-serif text-3xl font-semibold text-content">{{ number_format($stats['likes']) }}</dd>
                </div>
                <div class="card flex flex-col items-center gap-1 p-6 text-center">
                    <dt class="text-xs font-medium uppercase tracking-wider text-muted">Comments</dt>
                    <dd class="font-serif text-3xl font-semibold text-content">{{ number_format($stats['comments']) }}</dd>
                </div>
            </dl>
        </x-container>
    </section>

    <section>
        <x-container class="py-16 md:py-20">
            <div class="flex items-end justify-between">
                <div>
                    <p class="eyebrow">Writing</p>
                    <h2 class="mt-2 font-serif text-2xl font-semibold tracking-tight text-content">Posts by {{ $author->name }}</h2>
                </div>
                <a href="{{ route('authors.index') }}" class="hidden text-sm font-semibold text-primary hover:underline sm:inline">All authors →</a>
            </div>

            @if ($posts->isEmpty())
                <x-empty-state
                    icon="documents"
                    :title="$author->name.' hasn\'t published anything yet'"
                    description="Check back soon for their latest work."
                />
            @else
                <div class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($posts as $post)
                        <x-post-card
                            :title="$post->title"
                            :excerpt="$post->excerpt"
                            :category="$post->category?->name ?? 'Uncategorized'"
                            :date="$post->published_at?->format('M j, Y')"
                            :readTime="$post->reading_time.' min read'"
                            :author="$author->name"
                            :user="$author"
                            :authorHref="$author->author_url"
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
