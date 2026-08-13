<x-app-layout>
    <x-slot name="title">{{ __('Home') }}</x-slot>
    <x-slot name="metaDescription">{{ __('Stories worth reading, thoughtfully written. A premium editorial blog exploring technology, design, culture and ideas.') }}</x-slot>

    @push('jsonld')
        <x-json-ld :data="[
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => setting('general.site_name', config('app.name')),
            'url' => url('/'),
            'description' => setting('general.description', 'A premium editorial blog platform crafted with Laravel.'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => route('search').'?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ]" />
    @endpush

    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute -top-32 right-0 h-96 w-96 rounded-full bg-primary/10 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-40 -left-24 h-96 w-96 rounded-full bg-secondary/10 blur-3xl" aria-hidden="true"></div>

        <x-container class="py-16 md:py-24">
            <div class="grid items-center gap-14 lg:grid-cols-2">
                <div>
                    <p class="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary-soft px-3 py-1 text-xs font-semibold text-primary animate-fade-up">
                        <span class="h-1.5 w-1.5 rounded-full bg-primary"></span>
                        Editor's choice
                    </p>

                    <h1 class="mt-5 animate-fade-up font-serif text-4xl font-semibold leading-[1.1] tracking-tight text-content text-balance sm:text-5xl xl:text-6xl" style="animation-delay: 80ms">
                        Stories worth reading, thoughtfully written.
                    </h1>

                    <p class="mt-5 max-w-xl animate-fade-up text-lg leading-relaxed text-muted text-pretty" style="animation-delay: 160ms">
                        A modern editorial platform for writers and readers who believe ideas deserve depth, clarity and a little beauty.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3 animate-fade-up" style="animation-delay: 240ms">
                        <x-button href="{{ route('blog.index') }}" variant="primary" size="lg">
                            Start reading
                            <x-icon icon="arrow-right" class="h-4 w-4" />
                        </x-button>
                        @auth
                            <x-button href="{{ route('dashboard') }}" variant="outline" size="lg">Your dashboard</x-button>
                        @else
                            <x-button href="{{ route('register') }}" variant="outline" size="lg">Join the community</x-button>
                        @endauth
                    </div>

                    <dl class="mt-12 grid max-w-md grid-cols-3 gap-6 border-t border-line pt-8 animate-fade-up" style="animation-delay: 320ms">
                        <div>
                            <dt class="order-last mt-1 text-xs font-medium uppercase tracking-wider text-muted">Articles</dt>
                            <dd class="font-serif text-2xl font-semibold text-content sm:text-3xl">{{ number_format($stats['posts']) }}</dd>
                        </div>
                        <div>
                            <dt class="order-last mt-1 text-xs font-medium uppercase tracking-wider text-muted">Writers</dt>
                            <dd class="font-serif text-2xl font-semibold text-content sm:text-3xl">{{ number_format($stats['authors']) }}</dd>
                        </div>
                        <div>
                            <dt class="order-last mt-1 text-xs font-medium uppercase tracking-wider text-muted">Reads</dt>
                            <dd class="font-serif text-2xl font-semibold text-content sm:text-3xl">{{ number_format($stats['views']) }}</dd>
                        </div>
                    </dl>
                </div>

                @if ($hero)
                    <a href="{{ $hero->url }}" class="group relative mx-auto w-full max-w-lg animate-fade-in" style="animation-delay: 200ms">
                        <div class="absolute -inset-4 rounded-[2.5rem] bg-gradient-to-br from-primary/20 via-transparent to-secondary/20 blur-2xl" aria-hidden="true"></div>

                        <div class="card relative overflow-hidden">
                            <div class="relative aspect-[4/3] overflow-hidden {{ $hero->featured_image_url ? '' : 'bg-gradient-to-br from-primary via-secondary to-amber-400' }}">
                                @if ($hero->featured_image_url)
                                    <img src="{{ $hero->featured_image_url }}" alt="{{ $hero->title }}" loading="eager" fetchpriority="high" decoding="async" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                                @else
                                    <div class="flex h-full items-center justify-center">
                                        <span class="font-serif text-7xl font-bold text-white/20 select-none">BP</span>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent" aria-hidden="true"></div>
                            </div>

                            <div class="relative p-6 sm:p-7">
                                <x-badge variant="primary" class="absolute -top-3 left-6 shadow-soft">Featured</x-badge>

                                <p class="flex items-center gap-2 text-xs text-muted">
                                    <x-icon icon="sparkles" class="h-3.5 w-3.5 text-secondary" />
                                    Editor's choice · This week
                                </p>

                                <h2 class="mt-2 font-serif text-2xl font-semibold leading-snug tracking-tight text-content transition-colors group-hover:text-primary">
                                    {{ $hero->title }}
                                </h2>

                                <p class="mt-3 text-sm leading-relaxed text-muted line-clamp-2">
                                    {{ $hero->excerpt }}
                                </p>

                                <div class="mt-5 flex items-center gap-3 border-t border-line pt-4">
                                    <x-avatar :user="$hero->author" size="sm" />
                                    <div class="text-sm">
                                        <p class="font-medium text-content">{{ $hero->author?->name }}</p>
                                        <p class="text-xs text-muted">{{ $hero->published_at?->format('M j, Y') }} · {{ $hero->reading_time }} min read</p>
                                    </div>
                                    <span class="ms-auto text-muted transition-transform duration-200 group-hover:translate-x-1 group-hover:text-primary" aria-hidden="true">
                                        <x-icon icon="arrow-right" class="h-4 w-4" />
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @else
                    <div class="card relative mx-auto w-full max-w-lg p-10 text-center animate-fade-in">
                        <x-empty-state icon="documents" title="No stories yet" description="Our writers are crafting the first stories for the journal. Check back soon." />
                    </div>
                @endif
            </div>
        </x-container>
    </section>

    {{-- Featured posts --}}
    @if ($featured->isNotEmpty())
        <section class="border-t border-line bg-surface">
            <x-container class="py-16 md:py-20">
                <x-section-heading eyebrow="Featured" title="Handpicked this week" description="A curated selection of our best stories, chosen by the editorial team.">
                    <x-slot name="action">
                        <x-button href="{{ route('blog.index') }}" variant="ghost" size="sm">
                            View all
                            <x-icon icon="arrow-right" class="h-4 w-4" />
                        </x-button>
                    </x-slot>
                </x-section-heading>

                <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($featured as $post)
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
            </x-container>
        </section>
    @endif

    {{-- Latest posts --}}
    @if ($latest->isNotEmpty())
        <section>
            <x-container class="py-16 md:py-20">
                <x-section-heading eyebrow="Latest" title="Fresh from the editors" description="New stories published every week across every section.">
                    <x-slot name="action">
                        <x-button href="{{ route('blog.index') }}" variant="ghost" size="sm">
                            View all
                            <x-icon icon="arrow-right" class="h-4 w-4" />
                        </x-button>
                    </x-slot>
                </x-section-heading>

                <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($latest as $post)
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
            </x-container>
        </section>
    @endif

    {{-- Trending --}}
    @if ($trending->isNotEmpty())
        <section class="border-t border-line bg-surface">
            <x-container class="py-16 md:py-20">
                <x-section-heading eyebrow="Trending" title="Most read right now" description="The stories our readers couldn't put down this month." />

                <div class="mt-10 grid gap-6 lg:grid-cols-2">
                    <x-card padded class="divide-y divide-line">
                        @foreach ($trending as $index => $post)
                            <div class="group flex items-center gap-5 py-4 first:pt-0 last:pb-0">
                                <span class="font-serif text-3xl font-semibold text-primary/25 transition-colors group-hover:text-primary/50 select-none">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-serif text-lg font-semibold leading-snug text-content transition-colors group-hover:text-primary">
                                        <a href="{{ $post->url }}" class="focus-visible:outline-none">{{ $post->title }}</a>
                                    </h3>
                                    <p class="mt-1 text-xs text-muted">{{ $post->author?->name }} · {{ number_format($post->view_count) }} views</p>
                                </div>
                                <span class="text-muted transition-transform duration-200 group-hover:translate-x-1 group-hover:text-primary" aria-hidden="true">
                                    <x-icon icon="arrow-right" class="h-4 w-4" />
                                </span>
                            </div>
                        @endforeach
                    </x-card>

                    <div class="flex flex-col gap-6">
                        <x-card padded class="flex flex-1 flex-col justify-between bg-gradient-to-br from-primary to-primary-hover text-white">
                            <div>
                                <p class="eyebrow text-white/70">
                                    <span class="h-px w-6 bg-current"></span>
                                    Explore the archive
                                </p>
                                <h3 class="mt-3 font-serif text-2xl font-semibold leading-snug text-white">Find your next favorite story</h3>
                                <p class="mt-3 text-sm text-white/75">Browse every post by category, tag or author — or search the full archive.</p>
                            </div>
                            <div class="mt-6 flex flex-wrap gap-3">
                                <a href="{{ route('blog.index') }}" class="btn btn-md bg-white text-primary hover:bg-white/90">
                                    Browse all posts
                                    <x-icon icon="arrow-right" class="h-4 w-4" />
                                </a>
                                <a href="{{ route('search') }}" class="btn btn-md bg-white/10 text-white hover:bg-white/20">
                                    Search
                                </a>
                            </div>
                        </x-card>

                        <x-card padded class="flex items-center justify-between gap-4">
                            <div>
                                <h3 class="font-serif text-lg font-semibold text-content">Meet our writers</h3>
                                <p class="mt-1 text-sm text-muted">The people behind every story.</p>
                            </div>
                            <x-button href="{{ route('authors.index') }}" variant="outline" size="sm">All authors</x-button>
                        </x-card>
                    </div>
                </div>
            </x-container>
        </section>
    @endif

    {{-- Categories --}}
    @if ($categories->isNotEmpty())
        <section>
            <x-container class="py-16 md:py-20">
                <x-section-heading eyebrow="Explore" title="Browse by category" description="Our editorial sections, one thoughtful reading experience.">
                    <x-slot name="action">
                        <x-button href="{{ route('categories.index') }}" variant="ghost" size="sm">
                            All categories
                            <x-icon icon="arrow-right" class="h-4 w-4" />
                        </x-button>
                    </x-slot>
                </x-section-heading>

                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
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
            </x-container>
        </section>
    @endif

    {{-- Authors --}}
    @if ($authors->isNotEmpty())
        <section class="border-t border-line bg-surface">
            <x-container class="py-16 md:py-20">
                <x-section-heading eyebrow="Our writers" title="The people behind the words" description="A growing community of writers crafting thoughtful stories.">
                    <x-slot name="action">
                        <x-button href="{{ route('authors.index') }}" variant="ghost" size="sm">
                            All authors
                            <x-icon icon="arrow-right" class="h-4 w-4" />
                        </x-button>
                    </x-slot>
                </x-section-heading>

                <div class="mt-10 grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
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
            </x-container>
        </section>
    @endif

    {{-- Newsletter --}}
    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-primary-soft via-transparent to-secondary-soft" aria-hidden="true"></div>

        <x-container class="relative py-20 text-center md:py-24">
            <div class="mx-auto max-w-2xl">
                <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-primary text-primary-fg shadow-soft">
                    <x-icon icon="mail" class="h-6 w-6" />
                </span>

                <h2 class="mt-6 font-serif text-3xl font-semibold tracking-tight text-content text-balance md:text-4xl">
                    The Sunday Letter, from our editors
                </h2>
                <p class="mt-3 text-muted text-pretty">
                    One email a week with the best stories, essays and ideas. No spam — unsubscribe anytime.
                </p>

                <form class="mx-auto mt-8 flex max-w-md flex-col gap-3 sm:flex-row" x-data="newsletterForm()" x-on:submit.prevent="submit()">
                    <label for="hero-newsletter-email" class="sr-only">Email address</label>
                    <input
                        id="hero-newsletter-email"
                        type="email"
                        x-model="email"
                        placeholder="you@example.com"
                        class="input-field flex-1"
                        autocomplete="email"
                        required
                    >
                    <x-button type="submit" variant="primary" size="lg" x-bind:disabled="loading">
                        <span x-show="! loading">Subscribe</span>
                        <span x-show="loading">…</span>
                    </x-button>
                </form>

                <p x-show="error" x-transition x-cloak class="mx-auto mt-3 max-w-md text-sm font-medium text-danger" x-text="error"></p>
                <p x-show="success" x-transition x-cloak class="mx-auto mt-3 max-w-md text-sm font-semibold text-success" x-text="success"></p>
            </div>
        </x-container>
    </section>
</x-app-layout>
