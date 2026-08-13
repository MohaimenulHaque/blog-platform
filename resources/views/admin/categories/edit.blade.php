<x-admin-layout>
    <x-slot name="title">{{ __('Edit category') }}</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">Edit category</h1>
                <p class="mt-0.5 text-sm text-muted">{{ $category->name }}</p>
            </div>
            <x-button variant="outline" size="md" href="{{ route('admin.categories.index') }}">Cancel</x-button>
        </div>

        @if (session('status'))
            <x-alert type="success" :dismissible="true">{{ session('status') }}</x-alert>
        @endif

        <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            @include('admin.categories.partials.form', [
                'category' => $category,
                'statuses' => $statuses,
            ])

            <div class="mt-6 flex justify-end">
                <x-button variant="primary" size="lg" type="submit">Update category</x-button>
            </div>
        </form>
    </div>
</x-admin-layout>
