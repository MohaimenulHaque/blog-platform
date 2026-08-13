<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title') · {{ config('app.name') }}</title>

        <script>
            (function () {
                try {
                    var stored = localStorage.getItem('theme');
                    var dark = stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches);
                    document.documentElement.classList.toggle('dark', dark);
                } catch (e) {}
            })();
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:opsz,wght@9..144,400..700&family=inter:wght@400..800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="bg-background text-content font-sans antialiased">
        <div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden px-6 py-16 text-center">
            <div class="pointer-events-none absolute -top-32 left-1/2 h-96 w-96 -translate-x-1/2 rounded-full bg-primary/10 blur-3xl" aria-hidden="true"></div>

            <x-logo class="mb-12" />

            <p class="animate-fade-up select-none font-serif text-[6rem] font-semibold leading-none tracking-tight text-primary/15 md:text-[10rem]" aria-hidden="true">
                @yield('code')
            </p>

            <div class="relative -mt-8 animate-fade-up md:-mt-12" style="animation-delay: 80ms">
                <x-badge variant="primary" class="mb-5">Error @yield('code')</x-badge>

                <h1 class="font-serif text-3xl font-semibold tracking-tight text-content text-balance md:text-4xl">
                    @yield('title')
                </h1>

                <p class="mx-auto mt-3 max-w-md text-muted text-pretty">
                    @yield('message')
                </p>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <x-button href="{{ url('/') }}" variant="primary" size="lg">
                        Back to home
                    </x-button>
                    <x-button href="#" variant="outline" size="lg" onclick="history.back(); return false;">
                        Go back
                    </x-button>
                </div>
            </div>
        </div>
    </body>
</html>
