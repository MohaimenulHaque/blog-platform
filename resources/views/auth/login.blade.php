<x-guest-layout>
    <div>
        <x-button href="{{ route('home') }}" variant="ghost" size="sm" class="-ml-2 text-muted">
            <x-icon icon="arrow-right" class="h-4 w-4 rotate-180" />
            Back to home
        </x-button>

        <h2 class="mt-4 font-serif text-2xl font-semibold tracking-tight text-content">Welcome back</h2>
        <p class="mt-1 text-sm text-muted">Sign in to your account to continue reading and writing.</p>
    </div>

    <x-auth-session-status class="mt-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-content-soft">
                <input id="remember_me" type="checkbox" name="remember"
                    class="h-4 w-4 rounded border-line-strong bg-surface text-primary shadow-soft focus:ring-primary-ring">
                {{ __('Remember me') }}
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-primary transition-colors hover:text-primary-hover" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full">
            {{ __('Log in') }}
        </x-primary-button>

        <div class="relative my-2">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-line"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="bg-background px-3 text-xs uppercase tracking-widest text-muted">New here?</span>
            </div>
        </div>

        <p class="text-center text-sm text-muted">
            {{ __("Don't have an account?") }}
            <a href="{{ route('register') }}" class="font-semibold text-primary transition-colors hover:text-primary-hover">{{ __('Create one') }}</a>
        </p>
    </form>
</x-guest-layout>
