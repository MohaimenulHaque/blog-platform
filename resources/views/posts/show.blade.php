<x-app-layout>
    <x-slot name="title">{{ $post->title }}</x-slot>
    <x-slot name="metaDescription">{{ $post->meta_description ?? $post->excerpt }}</x-slot>
    <x-slot name="ogTitle">{{ $post->og_title ?? $post->meta_title ?? $post->title }}</x-slot>
    <x-slot name="ogDescription">{{ $post->og_description ?? $post->meta_description ?? $post->excerpt }}</x-slot>
    <x-slot name="ogType">article</x-slot>
    @if ($post->og_image)
        <x-slot name="ogImage">{{ asset('storage/'.$post->og_image) }}</x-slot>
    @elseif ($post->featured_image_url)
        <x-slot name="ogImage">{{ $post->featured_image_url }}</x-slot>
    @endif
    <x-slot name="canonical">{{ $post->url }}</x-slot>
    @if ($post->meta_keywords)
        <x-slot name="metaKeywords">{{ $post->meta_keywords }}</x-slot>
    @endif

    @push('head')
        <meta property="article:published_time" content="{{ $post->published_at?->toIso8601String() }}">
        <meta property="article:modified_time" content="{{ $post->updated_at?->toIso8601String() }}">
        @if ($post->author)
            <meta property="article:author" content="{{ $post->author->author_url }}">
        @endif
        @if ($post->category)
            <meta property="article:section" content="{{ $post->category->name }}">
        @endif
        @foreach ($post->tags as $tag)
            <meta property="article:tag" content="{{ $tag->name }}">
        @endforeach
    @endpush

    @php
        $postImage = $post->og_image ? asset('storage/'.$post->og_image) : ($post->featured_image_url ?? null);
    @endphp

    @push('jsonld')
        <x-json-ld :data="[
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => $post->meta_description ?? $post->excerpt,
            'image' => $postImage,
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $post->url,
            ],
            'author' => $post->author ? [
                '@type' => 'Person',
                'name' => $post->author->name,
                'url' => $post->author->author_url,
            ] : null,
            'publisher' => [
                '@type' => 'Organization',
                'name' => setting('general.site_name', config('app.name')),
                'logo' => setting('branding.logo') ? ['@type' => 'ImageObject', 'url' => asset('storage/'.setting('branding.logo'))] : null,
            ],
            'inLanguage' => app()->getLocale(),
        ]" />
        <x-json-ld :data="[
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect([
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Blog', 'url' => route('blog.index')],
                $post->category ? ['name' => $post->category->name, 'url' => $post->category->url] : null,
                ['name' => $post->title, 'url' => $post->url],
            ])->filter()->values()->map(fn ($item, $index) => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ])->all(),
        ]" />
        @if ($post->author)
            <x-json-ld :data="[
                '@context' => 'https://schema.org',
                '@type' => 'Person',
                '@id' => $post->author->author_url,
                'name' => $post->author->name,
                'url' => $post->author->author_url,
                'jobTitle' => $post->author->designation_label,
                'description' => $post->author->bio,
                'image' => $post->author->avatar_url,
                'sameAs' => array_values($post->author->social_links),
            ]" />
        @endif
    @endpush

    {{-- Reading progress --}}
    <div class="pointer-events-none fixed inset-x-0 top-0 z-50 h-0.5" x-data="{ progress: 0 }" x-init="$nextTick(() => {
        const el = $el;
        const update = () => {
            const doc = document.documentElement;
            const max = doc.scrollHeight - window.innerHeight;
            el.style.setProperty('--progress', String(max > 0 ? window.scrollY / max : 0));
        };
        window.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update, { passive: true });
        update();
    })">
        <div class="h-full origin-left bg-gradient-to-r from-primary to-secondary transition-transform duration-75" style="transform: scaleX(var(--progress, 0));"></div>
    </div>

    <article>
        {{-- Breadcrumb --}}
        <nav class="border-b border-line bg-surface" aria-label="Breadcrumb">
            <x-container class="py-4">
                <ol class="flex flex-wrap items-center gap-2 text-sm text-muted">
                    <li>
                        <a href="{{ route('home') }}" class="transition-colors hover:text-primary">Home</a>
                    </li>
                    <li aria-hidden="true"><x-icon icon="chevron-down" class="h-3.5 w-3.5 -rotate-90" /></li>
                    <li>
                        <a href="{{ route('blog.index') }}" class="transition-colors hover:text-primary">Blog</a>
                    </li>
                    @if ($post->category)
                        <li aria-hidden="true"><x-icon icon="chevron-down" class="h-3.5 w-3.5 -rotate-90" /></li>
                        <li>
                            <a href="{{ $post->category->url }}" class="transition-colors hover:text-primary">{{ $post->category->name }}</a>
                        </li>
                    @endif
                    <li aria-hidden="true"><x-icon icon="chevron-down" class="h-3.5 w-3.5 -rotate-90" /></li>
                    <li aria-current="page" class="line-clamp-1 max-w-[12rem] font-medium text-content">{{ $post->title }}</li>
                </ol>
            </x-container>
        </nav>

        {{-- Header --}}
        <header class="border-b border-line bg-surface">
            <x-container class="py-12 md:py-16">
                <div class="mx-auto max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($post->category)
                            <a href="{{ $post->category->url }}" class="eyebrow">{{ $post->category->name }}</a>
                        @endif
                        <span class="text-xs text-muted">·</span>
                        <span class="text-xs font-medium uppercase tracking-wider text-muted">{{ $post->published_at?->format('F j, Y') }}</span>
                    </div>

                    <h1 class="mt-4 font-serif text-4xl font-semibold leading-tight tracking-tight text-content text-balance md:text-5xl">
                        {{ $post->title }}
                    </h1>

                    @if ($post->excerpt)
                        <p class="mt-5 text-lg leading-relaxed text-muted text-pretty">{{ $post->excerpt }}</p>
                    @endif

                    <div class="mt-8 flex flex-wrap items-center justify-between gap-4 border-t border-line pt-6">
                        <a href="{{ $post->author?->author_url }}" class="flex items-center gap-3 group">
                            <x-avatar :user="$post->author" size="md" />
                            <div>
                                <p class="text-sm font-semibold text-content transition-colors group-hover:text-primary">{{ $post->author?->name }}</p>
                                <p class="text-xs text-muted">{{ $post->author?->designation_label }}</p>
                            </div>
                        </a>

                        <div class="flex flex-wrap items-center gap-4 text-sm text-muted">
                            <span class="inline-flex items-center gap-1.5">
                                <x-icon icon="clock" class="h-4 w-4" />
                                {{ $post->reading_time }} min read
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" /></svg>
                                {{ number_format($post->view_count) }} views
                            </span>
                            @if ($post->updated_at && $post->published_at && $post->updated_at->gt($post->published_at))
                                <span class="inline-flex items-center gap-1.5" title="Last updated">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 4 23 10 17 10" /><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10" /></svg>
                                    Updated {{ $post->updated_at->format('M j, Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </x-container>
        </header>

        <x-container class="py-14 md:py-20">
            <div class="grid gap-12 lg:grid-cols-12">
                {{-- Table of contents --}}
                <aside class="order-2 lg:order-1 lg:col-span-3" aria-label="Table of contents">
                    @if ($toc)
                        <div class="sticky top-24">
                            <div x-data="{ open: true }">
                                <button
                                    type="button"
                                    x-on:click="open = !open"
                                    class="flex w-full items-center justify-between rounded-xl border border-line bg-surface px-4 py-3 text-sm font-semibold text-content lg:cursor-default lg:border-0 lg:bg-transparent lg:px-0 lg:py-0"
                                    aria-expanded="true"
                                >
                                    <span>On this page</span>
                                    <x-icon icon="chevron-down" class="h-4 w-4 text-muted lg:hidden" x-bind:class="open ? 'rotate-180' : ''" />
                                </button>

                                <ol x-show="open" class="mt-3 space-y-1 border-t border-line pt-3 lg:block lg:space-y-0.5 lg:border-0 lg:pt-0" x-cloak>
                                    @foreach ($toc as $heading)
                                        <li>
                                            <a
                                                href="#{{ $heading['id'] }}"
                                                class="block border-l-2 py-1 text-sm leading-snug transition-colors hover:text-primary"
                                                @class([
                                                    'pl-3 text-content-soft' => $heading['level'] === 2,
                                                    'pl-7 text-muted' => $heading['level'] === 3,
                                                ])
                                            >
                                                {{ $heading['text'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    @endif
                </aside>

                {{-- Article body --}}
                <div class="order-1 lg:order-2 lg:col-span-9">
                    <div class="mx-auto max-w-3xl">
                        @if ($post->featured_image_url)
                            <figure class="mb-10">
                                <img
                                    src="{{ $post->featured_image_url }}"
                                    alt="{{ $post->title }}"
                                    loading="lazy"
                                    decoding="async"
                                    class="h-auto w-full rounded-3xl object-cover shadow-soft"
                                >
                            </figure>
                        @endif

                        @if ($article)
                            <div class="prose-blog">{!! $article !!}</div>
                        @else
                            <p class="text-muted">This post has no content yet.</p>
                        @endif

                        @if ($post->tags->isNotEmpty())
                            <div class="mt-12 border-t border-line pt-8">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">Tags</p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach ($post->tags as $tag)
                                        <a href="{{ $tag->url }}" class="inline-flex items-center rounded-full bg-surface-alt px-3 py-1.5 text-xs font-medium text-content-soft transition-colors hover:bg-primary-soft hover:text-primary">
                                            #{{ $tag->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Actions: like, bookmark, share --}}
                        <div
                            class="mt-10 border-t border-line pt-8"
                            x-data="postActions({
                                id: {{ $post->id }},
                                url: @js($post->url),
                                liked: {{ $liked ? 'true' : 'false' }},
                                likeCount: {{ $post->like_count }},
                                bookmarked: {{ $bookmarked ? 'true' : 'false' }},
                                auth: {{ auth()->check() ? 'true' : 'false' }},
                                loginUrl: @js(route('login')),
                            })"
                        >
                            <div class="flex flex-wrap items-center gap-3">
                                <button
                                    type="button"
                                    x-on:click="toggleLike()"
                                    x-bind:disabled="liking"
                                    class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition-all"
                                    x-bind:class="liked ? 'border-danger/30 bg-danger-soft text-danger' : 'border-line bg-surface text-content-soft hover:border-danger/30 hover:text-danger'"
                                    aria-label="Like post"
                                >
                                    <x-icon icon="heart" class="h-4 w-4" x-bind:class="liked ? 'fill-current' : ''" />
                                    <span x-show="! liked">Like</span>
                                    <span x-show="liked">Liked</span>
                                    <span class="text-xs font-medium text-muted" x-text="likeCount"></span>
                                </button>

                                <button
                                    type="button"
                                    x-on:click="toggleBookmark()"
                                    x-bind:disabled="bookmarking"
                                    class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition-all"
                                    x-bind:class="bookmarked ? 'border-primary/30 bg-primary-soft text-primary' : 'border-line bg-surface text-content-soft hover:border-primary/30 hover:text-primary'"
                                    aria-label="Bookmark post"
                                >
                                    <x-icon icon="bookmark" class="h-4 w-4" x-bind:class="bookmarked ? 'fill-current' : ''" />
                                    <span x-show="! bookmarked">Save</span>
                                    <span x-show="bookmarked">Saved</span>
                                </button>

                                <button
                                    type="button"
                                    x-on:click="copyLink()"
                                    x-bind:disabled="copying"
                                    class="inline-flex items-center gap-2 rounded-full border border-line bg-surface px-4 py-2 text-sm font-semibold text-content-soft transition-all hover:border-primary/30 hover:text-primary"
                                    aria-label="Copy link to post"
                                >
                                    <x-icon icon="link" class="h-4 w-4" />
                                    <span x-show="! copied">Copy link</span>
                                    <span x-show="copied">Link copied!</span>
                                </button>
                            </div>
                        </div>

                        {{-- Share row --}}
                        <div class="mt-10 flex flex-wrap items-center justify-between gap-4 border-t border-line pt-8">
                            <a href="{{ route('blog.index') }}" class="btn btn-outline btn-md">
                                <x-icon icon="arrow-right" class="h-4 w-4 rotate-180" />
                                All posts
                            </a>
                            <div class="flex items-center gap-4">
                                <span class="text-sm text-muted">Share</span>
                                <div class="flex items-center gap-2">
                                    <a
                                        href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($post->url) }}"
                                        target="_blank"
                                        rel="noopener nofollow"
                                        class="grid h-9 w-9 place-items-center rounded-full border border-line bg-surface text-muted transition-all duration-200 hover:border-primary hover:bg-primary hover:text-primary-fg"
                                        aria-label="Share on Facebook"
                                    >
                                        <x-icon icon="facebook" class="h-4 w-4" />
                                    </a>
                                    <a
                                        href="https://twitter.com/intent/tweet?url={{ urlencode($post->url) }}&text={{ urlencode($post->title) }}"
                                        target="_blank"
                                        rel="noopener nofollow"
                                        class="grid h-9 w-9 place-items-center rounded-full border border-line bg-surface text-muted transition-all duration-200 hover:border-primary hover:bg-primary hover:text-primary-fg"
                                        aria-label="Share on X"
                                    >
                                        <x-icon icon="twitter" class="h-4 w-4" />
                                    </a>
                                    <a
                                        href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($post->url) }}"
                                        target="_blank"
                                        rel="noopener nofollow"
                                        class="grid h-9 w-9 place-items-center rounded-full border border-line bg-surface text-muted transition-all duration-200 hover:border-primary hover:bg-primary hover:text-primary-fg"
                                        aria-label="Share on LinkedIn"
                                    >
                                        <x-icon icon="linkedin" class="h-4 w-4" />
                                    </a>
                                    <a
                                        href="https://wa.me/?text={{ urlencode($post->title.' '.$post->url) }}"
                                        target="_blank"
                                        rel="noopener nofollow"
                                        class="grid h-9 w-9 place-items-center rounded-full border border-line bg-surface text-muted transition-all duration-200 hover:border-primary hover:bg-primary hover:text-primary-fg"
                                        aria-label="Share on WhatsApp"
                                    >
                                        <x-icon icon="whatsapp" class="h-4 w-4" />
                                    </a>
                                    <a
                                        href="https://t.me/share/url?url={{ urlencode($post->url) }}&text={{ urlencode($post->title) }}"
                                        target="_blank"
                                        rel="noopener nofollow"
                                        class="grid h-9 w-9 place-items-center rounded-full border border-line bg-surface text-muted transition-all duration-200 hover:border-primary hover:bg-primary hover:text-primary-fg"
                                        aria-label="Share on Telegram"
                                    >
                                        <x-icon icon="telegram" class="h-4 w-4" />
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Author box --}}
                        @if ($post->author)
                            <aside class="card mt-10 flex flex-col gap-5 p-6 sm:flex-row sm:items-center" aria-label="About the author">
                                <a href="{{ $post->author->author_url }}" class="shrink-0">
                                    <x-avatar :user="$post->author" size="xl" class="ring-4 ring-line/50" />
                                </a>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary">About the author</p>
                                    <h2 class="mt-1 font-serif text-xl font-semibold text-content">
                                        <a href="{{ $post->author->author_url }}" class="transition-colors hover:text-primary">{{ $post->author->name }}</a>
                                    </h2>
                                    <p class="text-xs font-medium text-muted">{{ $post->author->designation_label }}</p>
                                    @if ($post->author->bio)
                                        <p class="mt-3 text-sm leading-relaxed text-muted text-pretty">{{ $post->author->bio }}</p>
                                    @endif
                                    <div class="mt-4 flex flex-wrap items-center gap-3">
                                        <x-button href="{{ $post->author->author_url }}" variant="outline" size="sm">
                                            View profile
                                            <x-icon icon="arrow-right" class="h-4 w-4" />
                                        </x-button>
                                        @if ($post->author->social_links)
                                            <div class="flex items-center gap-2">
                                                @foreach ($post->author->social_links as $platform => $url)
                                                    <a
                                                        href="{{ $url }}"
                                                        target="_blank"
                                                        rel="noopener nofollow"
                                                        class="grid h-9 w-9 place-items-center rounded-full border border-line bg-surface text-muted transition-all duration-200 hover:border-primary hover:bg-primary hover:text-primary-fg"
                                                        aria-label="{{ $post->author->name }} on {{ ucfirst($platform) }}"
                                                    >
                                                        <x-social-icon :url="$url" :label="$platform" :icon="$platform" class="h-4 w-4" />
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </aside>
                        @endif

                        {{-- Previous / next --}}
                        @if ($previous || $next)
                            <nav class="mt-10 grid gap-4 sm:grid-cols-2" aria-label="Post navigation">
                                @if ($previous)
                                    <a href="{{ $previous->url }}" class="card card-hover group flex items-center gap-4 p-5">
                                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-surface-alt text-muted transition-colors group-hover:bg-primary-soft group-hover:text-primary">
                                            <x-icon icon="arrow-right" class="h-4 w-4 rotate-180" />
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block text-xs text-muted">Previous post</span>
                                            <span class="mt-0.5 line-clamp-2 block text-sm font-semibold text-content transition-colors group-hover:text-primary">{{ $previous->title }}</span>
                                        </span>
                                    </a>
                                @else
                                    <span></span>
                                @endif

                                @if ($next)
                                    <a href="{{ $next->url }}" class="card card-hover group flex items-center justify-end gap-4 p-5 text-right">
                                        <span class="min-w-0">
                                            <span class="block text-xs text-muted">Next post</span>
                                            <span class="mt-0.5 line-clamp-2 block text-sm font-semibold text-content transition-colors group-hover:text-primary">{{ $next->title }}</span>
                                        </span>
                                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-surface-alt text-muted transition-colors group-hover:bg-primary-soft group-hover:text-primary">
                                            <x-icon icon="arrow-right" class="h-4 w-4" />
                                        </span>
                                    </a>
                                @endif
                            </nav>
                        @endif
                    </div>
                </div>
            </div>
        </x-container>
    </article>

    {{-- Comments --}}
    <section class="border-t border-line bg-surface" aria-labelledby="comments-heading">
        <x-container class="py-16">
            <div class="mx-auto max-w-3xl">
                <h2 id="comments-heading" class="font-serif text-2xl font-semibold tracking-tight text-content">
                    Comments
                    <span class="text-lg font-normal text-muted">({{ number_format($commentCount) }})</span>
                </h2>

                <div class="mt-8">
                    @auth
                        <form
                            x-data="commentForm({ postUrl: @js(route('posts.comments.store', $post)) })"
                            x-on:submit.prevent="submit()"
                            class="card p-5"
                            aria-label="Comment form"
                        >
                            <div class="flex gap-3">
                                <x-avatar :user="auth()->user()" size="sm" class="mt-1 shrink-0" />
                                <div class="flex-1">
                                    <textarea
                                        x-model="body"
                                        rows="3"
                                        class="input w-full"
                                        placeholder="Share your thoughts…"
                                        required
                                    ></textarea>
                                </div>
                            </div>

                            <p x-show="error" class="mt-3 text-sm text-danger" x-text="error"></p>
                            <p x-show="success" class="mt-3 text-sm text-success" x-text="success"></p>

                            <div class="mt-4 flex justify-end">
                                <button type="submit" class="btn btn-primary btn-md" x-bind:disabled="loading">
                                    <span x-show="! loading">Post comment</span>
                                    <span x-show="loading">Posting…</span>
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="card flex flex-col items-start gap-3 p-5">
                            <p class="text-sm text-muted">
                                <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">Log in</a>
                                to join the conversation.
                            </p>
                        </div>
                    @endauth
                </div>

                <div id="comments-list" class="mt-10 space-y-6">
                    @forelse ($comments as $comment)
                        @include('comments.partials.comment', ['comment' => $comment])
                    @empty
                        <div class="rounded-2xl border border-dashed border-line py-12 text-center">
                            <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-surface-alt text-muted">
                                <x-icon icon="message" class="h-6 w-6" />
                            </div>
                            <p class="mt-4 text-sm font-medium text-content-soft">No comments yet</p>
                            <p class="mt-1 text-sm text-muted">Be the first to share your thoughts.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </x-container>
    </section>

    @if ($related->isNotEmpty())
        <section class="border-t border-line bg-surface">
            <x-container class="py-16">
                <div class="flex items-end justify-between">
                    <div>
                        <p class="eyebrow">Keep reading</p>
                        <h2 class="mt-2 font-serif text-2xl font-semibold tracking-tight text-content">More to explore</h2>
                    </div>
                    <a href="{{ route('blog.index') }}" class="hidden text-sm font-semibold text-primary hover:underline sm:inline">View all posts →</a>
                </div>

                <div class="mt-8 grid gap-6 md:grid-cols-3">
                    @foreach ($related as $relatedPost)
                        <x-post-card
                            :title="$relatedPost->title"
                            :excerpt="$relatedPost->excerpt"
                            :category="$relatedPost->category?->name ?? 'Uncategorized'"
                            :date="$relatedPost->published_at?->format('M j, Y')"
                            :readTime="$relatedPost->reading_time.' min read'"
                            :author="$relatedPost->author?->name"
                            :user="$relatedPost->author"
                            :authorHref="$relatedPost->author?->author_url"
                            :views="$relatedPost->view_count"
                            :href="$relatedPost->url"
                            :image="$relatedPost->featured_image_url"
                        />
                    @endforeach
                </div>
            </x-container>
        </section>
    @endif
</x-app-layout>
