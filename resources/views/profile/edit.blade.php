<x-app-layout>
    <x-slot name="noindex">true</x-slot>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">Profile</p>
                <h2 class="mt-1 font-serif text-2xl font-semibold tracking-tight text-content">
                    {{ __('Account settings') }}
                </h2>
            </div>
            <x-avatar :user="$user" size="md" />
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto w-full max-w-5xl px-4 sm:px-6">
            <div class="grid gap-6 lg:grid-cols-5">
                <div class="space-y-6 lg:col-span-3">
                    <x-card padded>
                        @include('profile.partials.update-profile-information-form')
                    </x-card>

                    <x-card padded>
                        @include('profile.partials.update-password-form')
                    </x-card>
                </div>

                <div class="space-y-6 lg:col-span-2">
                    <x-card padded>
                        @include('profile.partials.account-settings')
                    </x-card>

                    <x-card padded>
                        @include('profile.partials.delete-user-form')
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
