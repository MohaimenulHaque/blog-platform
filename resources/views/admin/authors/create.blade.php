<x-admin-layout>
    <x-slot name="title">{{ __('New author') }}</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">New author</h1>
                <p class="mt-0.5 text-sm text-muted">Create a user account with the author role.</p>
            </div>
            <x-button variant="outline" size="md" href="{{ route('admin.authors.index') }}">Cancel</x-button>
        </div>

        <form method="POST" action="{{ route('admin.authors.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.authors.partials.form', ['socialPlatforms' => $socialPlatforms])

            <div class="mt-6 flex justify-end">
                <x-button variant="primary" size="lg" type="submit">Create author</x-button>
            </div>
        </form>
    </div>
</x-admin-layout>
