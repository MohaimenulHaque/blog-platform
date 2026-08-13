<x-admin-layout>
    <x-slot name="title">{{ __('New tag') }}</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">New tag</h1>
                <p class="mt-0.5 text-sm text-muted">Create a label to attach to posts.</p>
            </div>
            <x-button variant="outline" size="md" href="{{ route('admin.tags.index') }}">Cancel</x-button>
        </div>

        <form method="POST" action="{{ route('admin.tags.store') }}">
            @csrf
            @include('admin.tags.partials.form')

            <div class="mt-6 flex justify-end">
                <x-button variant="primary" size="lg" type="submit">Create tag</x-button>
            </div>
        </form>
    </div>
</x-admin-layout>
