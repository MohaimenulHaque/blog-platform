<x-app-layout>
    <x-slot name="title">{{ __('Unsubscribe') }}</x-slot>
    <x-slot name="noindex">true</x-slot>
    <x-slot name="metaDescription">{{ __('Unsubscribe from the newsletter.') }}</x-slot>

    <section>
        <x-container class="py-20 md:py-28">
            <div class="mx-auto max-w-md text-center">
                <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-surface-alt text-muted">
                    <x-icon icon="mail" class="h-7 w-7" />
                </div>

                @if ($subscriber->isSubscribed())
                    <h1 class="mt-6 font-serif text-3xl font-semibold tracking-tight text-content">Unsubscribe from the newsletter?</h1>
                    <p class="mt-3 text-sm leading-relaxed text-muted">
                        You will stop receiving email updates from <strong class="text-content">{{ config('app.name') }}</strong>
                        at <strong class="text-content">{{ $subscriber->email }}</strong>.
                    </p>

                    <form method="POST" action="{{ route('newsletter.unsubscribe.confirm', $subscriber->token) }}" class="mt-8 space-y-4">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg w-full">Unsubscribe</button>
                        <p class="text-xs text-muted">
                            Changed your mind? You can resubscribe at any time from the blog footer.
                        </p>
                    </form>
                @else
                    <h1 class="mt-6 font-serif text-3xl font-semibold tracking-tight text-content">You are unsubscribed</h1>
                    <p class="mt-3 text-sm leading-relaxed text-muted">
                        <strong class="text-content">{{ $subscriber->email }}</strong> will no longer receive newsletter
                        updates from {{ config('app.name') }}.
                    </p>

                    @if (session('status'))
                        <x-alert type="success" :dismissible="true" class="mt-6">{{ session('status') }}</x-alert>
                    @endif

                    <a href="{{ route('home') }}" class="btn btn-outline btn-md mt-8">
                        Back to the blog
                        <x-icon icon="arrow-right" class="h-4 w-4" />
                    </a>
                @endif
            </div>
        </x-container>
    </section>
</x-app-layout>
