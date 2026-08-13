<x-guest-layout>
    <div>
        <x-button href="{{ route('login') }}" variant="ghost" size="sm" class="-ml-2 text-muted">
            <x-icon icon="arrow-right" class="h-4 w-4 rotate-180" />
            Back to login
        </x-button>

        <h2 class="mt-4 font-serif text-2xl font-semibold tracking-tight text-content">Reset your password</h2>
        <p class="mt-1 text-sm text-muted">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </p>
    </div>

    <x-auth-session-status class="mt-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">
            {{ __('Email Password Reset Link') }}
        </x-primary-button>
    </form>
</x-guest-layout>
