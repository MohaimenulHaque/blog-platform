<x-guest-layout>
    <div>
        <h2 class="font-serif text-2xl font-semibold tracking-tight text-content">Verify your email</h2>
        <p class="mt-1 text-sm text-muted">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mt-6">
            <x-alert type="success" title="{{ __('Verification link sent') }}">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </x-alert>
        </div>
    @endif

    <div class="mt-8 flex items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}" class="flex-1">
            @csrf

            <x-primary-button class="w-full">
                {{ __('Resend Verification Email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="btn btn-outline btn-md">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
