<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $metaDescription ?? 'A premium editorial blog platform crafted with Laravel.' }}">

        @if (! empty($noindex))
            <meta name="robots" content="noindex, nofollow">
        @endif

        @php
            $siteName = setting('general.site_name', config('app.name'));
            $siteDescription = setting('general.description', 'A premium editorial blog platform crafted with Laravel.');
            $favicon = setting('branding.favicon');
            $defaultMetaTitle = setting('seo.meta_title');
            $defaultMetaDescription = setting('seo.meta_description', $siteDescription);
            $defaultMetaKeywords = setting('seo.meta_keywords');

            $pageTitle = isset($title) ? $title.' · '.$siteName : ($defaultMetaTitle ?: $siteName);
            $canonical = $canonical ?? url()->current();
            $ogTitle = $ogTitle ?? $pageTitle;
            $ogDescription = $ogDescription ?? ($metaDescription ?? $defaultMetaDescription);
            $ogImage = $ogImage ?? (setting('branding.og_image') ? asset('storage/'.setting('branding.og_image')) : null);
            $ogType = $ogType ?? 'website';
            $twitterHandle = setting('social.twitter');
        @endphp

        @if ($favicon)
            <link rel="icon" type="image/png" href="{{ asset('storage/'.$favicon) }}">
        @endif

        <title>{{ $pageTitle }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <link rel="canonical" href="{{ $canonical }}">

        @if (isset($metaKeywords) && filled($metaKeywords))
            <meta name="keywords" content="{{ $metaKeywords }}">
        @elseif ($defaultMetaKeywords)
            <meta name="keywords" content="{{ $defaultMetaKeywords }}">
        @endif

        <meta property="og:site_name" content="{{ $siteName }}">
        <meta property="og:title" content="{{ $ogTitle }}">
        <meta property="og:description" content="{{ $ogDescription }}">
        <meta property="og:type" content="{{ $ogType }}">
        <meta property="og:url" content="{{ $canonical }}">
        @if ($ogImage)
            <meta property="og:image" content="{{ $ogImage }}">
            <meta property="og:image:width" content="1200">
            <meta property="og:image:height" content="630">
        @endif

        <meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
        <meta name="twitter:title" content="{{ $ogTitle }}">
        <meta name="twitter:description" content="{{ $ogDescription }}">
        @if ($twitterHandle)
            <meta name="twitter:site" content="@{{ $twitterHandle }}">
        @endif
        @if ($ogImage)
            <meta name="twitter:image" content="{{ $ogImage }}">
        @endif

        <script>
            (function () {
                try {
                    var stored = localStorage.getItem('theme');
                    var dark = stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches);
                    document.documentElement.classList.toggle('dark', dark);

                    window.__theme = {
                        toggle: function () {
                            var next = !document.documentElement.classList.contains('dark');
                            document.documentElement.classList.toggle('dark', next);
                            localStorage.setItem('theme', next ? 'dark' : 'light');
                        },
                    };
                } catch (e) {}
            })();
        </script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @if ($trackingId = setting('analytics.tracking_id'))
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $trackingId }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag() { dataLayer.push(arguments); }
                gtag('js', new Date());
                gtag('config', '{{ $trackingId }}');
            </script>
        @endif

        @stack('head')
        @stack('jsonld')
    </head>

    <body class="bg-background text-content font-sans antialiased">
        <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-xl focus:bg-primary focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-primary-fg focus:shadow-lift">
            {{ __('Skip to content') }}
        </a>

        <x-navbar />

        @isset($header)
            <section class="border-b border-line bg-surface">
                <x-container class="py-8">
                    {{ $header }}
                </x-container>
            </section>
        @endisset

        <main id="main" class="min-h-[60vh]">
            {{ $slot }}
        </main>

        <x-footer />

        @stack('scripts')
    </body>
</html>
