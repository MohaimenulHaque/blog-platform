<x-admin-layout>
    <x-slot name="title">{{ __('Edit author') }}</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">Edit author</h1>
                <p class="mt-0.5 text-sm text-muted">{{ $author->name }}</p>
            </div>
            <x-button variant="outline" size="md" href="{{ route('admin.authors.index') }}">Cancel</x-button>
        </div>

        @if (session('status'))
            <x-alert type="success" :dismissible="true">{{ session('status') }}</x-alert>
        @endif

        <form method="POST" action="{{ route('admin.authors.update', $author) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            @include('admin.authors.partials.form', [
                'author' => $author,
                'socialPlatforms' => $socialPlatforms,
            ])

            <div class="mt-6 flex justify-end">
                <x-button variant="primary" size="lg" type="submit">Update author</x-button>
            </div>
        </form>
    </div>
</x-admin-layout>
