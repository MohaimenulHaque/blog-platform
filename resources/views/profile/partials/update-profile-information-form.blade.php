<section>
    <header>
        <h2 class="font-serif text-lg font-semibold text-content">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-muted">
            {{ __("Update your account's profile information, avatar and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="flex items-center gap-5">
            <x-avatar :user="$user" size="2xl" />

            <div class="min-w-0 flex-1">
                <x-input-label for="avatar" :value="__('Avatar')" />
                <input
                    id="avatar"
                    name="avatar"
                    type="file"
                    accept="image/*"
                    class="mt-1.5 block w-full text-sm text-muted file:mr-3 file:rounded-lg file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary-fg hover:file:bg-primary-hover"
                >
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                <p class="mt-2 text-xs text-muted">{{ __('PNG, JPG, GIF or WebP up to 2MB.') }}</p>
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="username" :value="__('Username')" />
            <div class="relative mt-1">
                <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-sm text-muted">@</span>
                <x-text-input id="username" name="username" type="text" class="block w-full pl-7" :value="old('username', $user->username)" autocomplete="username" placeholder="janedoe" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
            <p class="mt-2 text-xs text-muted">{{ __('Letters, numbers, dashes and underscores. Used as your public handle.') }}</p>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-content-soft">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-primary hover:text-primary-hover rounded-md focus:outline-none focus:ring-2 focus:ring-primary-ring">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-success">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="bio" :value="__('Bio')" />
            <x-textarea id="bio" name="bio" class="mt-1 block w-full" rows="3" placeholder="{{ __('Tell readers a little about yourself.') }}">{{ old('bio', $user->bio) }}</x-textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
            <p class="mt-2 text-xs text-muted">{{ __('Max 500 characters.') }}</p>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-success"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
