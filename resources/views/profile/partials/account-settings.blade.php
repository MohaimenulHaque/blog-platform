<section>
    <header>
        <h2 class="font-serif text-lg font-semibold text-content">
            {{ __('Account') }}
        </h2>

        <p class="mt-1 text-sm text-muted">
            {{ __('A snapshot of your account and membership.') }}
        </p>
    </header>

    <dl class="mt-6 space-y-5">
        <div class="flex items-center justify-between gap-4">
            <dt class="text-sm text-muted">{{ __('Role') }}</dt>
            <dd>
                <x-badge variant="{{ $user->isAdmin() ? 'danger' : ($user->isEditor() ? 'secondary' : ($user->isAuthor() ? 'primary' : 'neutral')) }}">
                    {{ $user->role?->name ?? __('User') }}
                </x-badge>
            </dd>
        </div>

        <div class="flex items-center justify-between gap-4">
            <dt class="text-sm text-muted">{{ __('Handle') }}</dt>
            <dd class="text-sm font-medium text-content">{{ $user->handle }}</dd>
        </div>

        <div class="flex items-center justify-between gap-4">
            <dt class="text-sm text-muted">{{ __('Member since') }}</dt>
            <dd class="text-sm font-medium text-content">{{ $user->created_at->format('M j, Y') }}</dd>
        </div>

        <div class="flex items-center justify-between gap-4">
            <dt class="text-sm text-muted">{{ __('Email status') }}</dt>
            <dd>
                @if ($user->hasVerifiedEmail())
                    <x-badge variant="success">
                        <x-icon icon="close" class="h-3.5 w-3.5 rotate-45" />
                        {{ __('Verified') }}
                    </x-badge>
                @else
                    <x-badge variant="warning">{{ __('Unverified') }}</x-badge>
                @endif
            </dd>
        </div>
    </dl>
</section>
