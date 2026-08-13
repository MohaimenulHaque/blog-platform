<x-guest-layout>
    <div>
        <x-button href="{{ route('home') }}" variant="ghost" size="sm" class="-ml-2 text-muted">
            <x-icon icon="arrow-right" class="h-4 w-4 rotate-180" />
            Back to home
        </x-button>

        <h2 class="mt-4 font-serif text-2xl font-semibold tracking-tight text-content">Confirm your password</h2>
        <p class="mt-1 text-sm text-muted">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">
            {{ __('Confirm') }}
        </x-primary-button>
    </form>
</x-guest-layout>
