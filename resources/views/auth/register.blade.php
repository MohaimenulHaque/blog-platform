<x-guest-layout>
    <div>
        <x-button href="{{ route('home') }}" variant="ghost" size="sm" class="-ml-2 text-muted">
            <x-icon icon="arrow-right" class="h-4 w-4 rotate-180" />
            Back to home
        </x-button>

        <h2 class="mt-4 font-serif text-2xl font-semibold tracking-tight text-content">Create your account</h2>
        <p class="mt-1 text-sm text-muted">Join the community and start writing with us.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Jane Doe" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="new-password" placeholder="At least 8 characters" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat your password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">
            {{ __('Create account') }}
        </x-primary-button>

        <p class="text-center text-sm text-muted">
            {{ __('Already have an account?') }}
            <a href="{{ route('login') }}" class="font-semibold text-primary transition-colors hover:text-primary-hover">{{ __('Log in') }}</a>
        </p>
    </form>
</x-guest-layout>
