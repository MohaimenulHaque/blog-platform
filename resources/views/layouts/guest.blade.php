<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">
        <meta name="description" content="{{ $metaDescription ?? 'A premium editorial blog platform crafted with Laravel.' }}">

        @php
            $pageTitle = isset($title) ? $title.' · '.config('app.name') : config('app.name');
            $canonical = $canonical ?? url()->current();
        @endphp

        <title>{{ $pageTitle }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <link rel="canonical" href="{{ $canonical }}">

        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $metaDescription ?? 'A premium editorial blog platform crafted with Laravel.' }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ $canonical }}">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="{{ $pageTitle }}">
        <meta name="twitter:description" content="{{ $metaDescription ?? 'A premium editorial blog platform crafted with Laravel.' }}">

        <script>
            (function () {
                try {
                    var stored = localStorage.getItem('theme');
                    var dark = stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches);
                    document.documentElement.classList.toggle('dark', dark);
                } catch (e) {}
            })();
        </script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

        <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('head')
    </head>

    <body class="bg-background text-content font-sans antialiased">
        <div class="flex min-h-screen">
            <aside class="relative hidden w-1/2 flex-col justify-between overflow-hidden bg-gradient-to-br from-primary via-primary-hover to-content p-12 lg:flex">
                <div class="pointer-events-none absolute -left-24 -top-24 h-80 w-80 rounded-full bg-white/10 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-32 -right-16 h-96 w-96 rounded-full bg-secondary/30 blur-3xl"></div>

                <div class="relative">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-white/10 text-white backdrop-blur">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 19l7-7 3 3-7 7-3-3z" />
                                <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z" />
                                <path d="M2 2l7.586 7.586" />
                                <circle cx="11" cy="11" r="2" />
                            </svg>
                        </span>
                        <span class="font-serif text-xl font-bold tracking-tight text-white">BlogPlatform</span>
                    </a>
                </div>

                <div class="relative max-w-md">
                    <h2 class="font-serif text-4xl font-semibold leading-tight tracking-tight text-white text-balance">
                        Stories worth reading, thoughtfully written.
                    </h2>
                    <p class="mt-4 text-white/70">
                        Join a growing community of writers and readers exploring ideas with depth and elegance.
                    </p>

                    <ul class="mt-8 space-y-3 text-sm text-white/80">
                        <li class="flex items-center gap-3">
                            <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-white/10">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5" /></svg>
                            </span>
                            A beautiful, distraction-free reading experience
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-white/10">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5" /></svg>
                            </span>
                            Thoughtful writing across every category
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-white/10">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5" /></svg>
                            </span>
                            Built for readers, designed for writers
                        </li>
                    </ul>
                </div>

                <p class="relative text-sm text-white/50">© {{ date('Y') }} {{ config('app.name') }} — Editorial platform</p>
            </aside>

            <main class="flex w-full flex-col items-center justify-center px-6 py-12 lg:w-1/2">
                <div class="w-full max-w-md">
                    <div class="mb-8 lg:hidden">
                        <x-logo />
                    </div>

                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
