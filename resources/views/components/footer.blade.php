<footer class="border-t border-line bg-surface">
    @php
        $siteDescription = setting('general.description', 'A premium editorial blog platform for thoughtful writing. Discover stories, ideas and perspectives from writers around the world.');
        $footerText = setting('branding.footer_text');
        $contactEmail = setting('general.email', config('blog.contact_recipient') ?? config('mail.from.address'));
        $contactPhone = setting('general.phone');
        $contactAddress = setting('general.address');
        $socialLinks = collect(['twitter', 'facebook', 'instagram', 'linkedin', 'youtube'])
            ->map(fn ($network) => ['icon' => $network, 'url' => setting('social.'.$network)])
            ->filter(fn ($link) => filled($link['url']))
            ->values()
            ->all();
        $socialLinks = count($socialLinks) > 0 ? $socialLinks : config('navigation.social');
    @endphp

    <x-container class="py-12 md:py-16">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <x-logo />

                <p class="mt-4 max-w-sm text-sm leading-relaxed text-muted text-pretty">
                    {{ $siteDescription }}
                </p>

                <div class="mt-6 flex items-center gap-2.5">
                    @foreach ($socialLinks as $social)
                        <x-social-icon :url="$social['url']" :label="$social['label'] ?? $social['icon']" :icon="$social['icon']" />
                    @endforeach
                </div>
            </div>

            <div class="lg:col-span-2">
                <h3 class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">Navigation</h3>
                <ul class="mt-4 space-y-2.5">
                    @foreach (config('navigation.footer') as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" class="text-sm text-content-soft transition-colors hover:text-primary">
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h3 class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">Contact</h3>
                <ul class="mt-4 space-y-3 text-sm text-content-soft">
                    <li class="flex items-start gap-3">
                        <x-icon icon="mail" class="mt-0.5 h-4 w-4 shrink-0 text-muted" />
                        <a href="mailto:{{ $contactEmail }}" class="transition-colors hover:text-primary">
                            {{ $contactEmail }}
                        </a>
                    </li>
                    @if ($contactPhone)
                        <li class="flex items-start gap-3">
                            <x-icon icon="phone" class="mt-0.5 h-4 w-4 shrink-0 text-muted" />
                            <a href="tel:{{ $contactPhone }}" class="transition-colors hover:text-primary">{{ $contactPhone }}</a>
                        </li>
                    @endif
                    @if ($contactAddress)
                        <li class="flex items-start gap-3">
                            <x-icon icon="map-pin" class="mt-0.5 h-4 w-4 shrink-0 text-muted" />
                            <span>{{ $contactAddress }}</span>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h3 class="text-xs font-semibold uppercase tracking-[0.18em] text-muted">Newsletter</h3>
                <p class="mt-4 text-sm text-muted">
                    The best stories, delivered to your inbox. No spam, ever.
                </p>

                <form class="mt-4" x-data="newsletterForm()" x-on:submit.prevent="submit()">
                    <div class="flex gap-2">
                        <label for="newsletter-email" class="sr-only">Email address</label>
                        <input
                            id="newsletter-email"
                            type="email"
                            x-model="email"
                            placeholder="you@example.com"
                            class="input-field"
                            autocomplete="email"
                            required
                        >
                        <x-button type="submit" variant="primary" x-bind:disabled="loading">
                            <span x-show="! loading">Subscribe</span>
                            <span x-show="loading">…</span>
                        </x-button>
                    </div>

                    <p x-show="error" x-transition x-cloak class="mt-3 text-sm font-medium text-danger" x-text="error"></p>
                    <p x-show="success" x-transition x-cloak class="mt-3 text-sm font-medium text-success" x-text="success"></p>
                </form>
            </div>
        </div>

        <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-line pt-6 sm:flex-row">
            <p class="text-xs text-muted">
                © {{ date('Y') }} {{ config('app.name') }}. {{ $footerText ?: 'All rights reserved.' }}
            </p>

            <nav class="flex flex-wrap items-center justify-center gap-6 text-xs text-muted" aria-label="Legal">
                <a href="#" class="transition-colors hover:text-content">Privacy Policy</a>
                <a href="#" class="transition-colors hover:text-content">Terms of Service</a>
            </nav>
        </div>
    </x-container>
</footer>
