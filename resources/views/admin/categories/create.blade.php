<x-admin-layout>
    <x-slot name="title">{{ __('New category') }}</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">New category</h1>
                <p class="mt-0.5 text-sm text-muted">Create a topic to group posts under.</p>
            </div>
            <x-button variant="outline" size="md" href="{{ route('admin.categories.index') }}">Cancel</x-button>
        </div>

        <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.categories.partials.form', ['statuses' => $statuses])

            <div class="mt-6 flex justify-end">
                <x-button variant="primary" size="lg" type="submit">Create category</x-button>
            </div>
        </form>
    </div>
</x-admin-layout>
