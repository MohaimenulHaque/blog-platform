<x-admin-layout>
    <x-slot name="title">{{ __('Edit tag') }}</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">Edit tag</h1>
                <p class="mt-0.5 text-sm text-muted">#{{ $tag->name }}</p>
            </div>
            <x-button variant="outline" size="md" href="{{ route('admin.tags.index') }}">Cancel</x-button>
        </div>

        @if (session('status'))
            <x-alert type="success" :dismissible="true">{{ session('status') }}</x-alert>
        @endif

        <form method="POST" action="{{ route('admin.tags.update', $tag) }}">
            @csrf
            @method('PATCH')
            @include('admin.tags.partials.form', ['tag' => $tag])

            <div class="mt-6 flex justify-end">
                <x-button variant="primary" size="lg" type="submit">Update tag</x-button>
            </div>
        </form>
    </div>
</x-admin-layout>
