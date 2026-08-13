<x-admin-layout>
    <x-slot name="title">{{ __('Edit post') }}</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">Edit post</h1>
                <p class="mt-0.5 text-sm text-muted">{{ $post->title }}</p>
            </div>
            <div class="flex items-center gap-2">
                <x-button variant="outline" size="md" href="{{ route('admin.posts.show', $post) }}">View</x-button>
                <x-button variant="ghost" size="md" href="{{ route('admin.posts.index') }}">Cancel</x-button>
            </div>
        </div>

        @if (session('status'))
            <x-alert type="success" :dismissible="true">{{ session('status') }}</x-alert>
        @endif

        <form method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            @include('admin.posts.partials.form', [
                'post' => $post,
                'categories' => $categories,
                'tags' => $tags,
                'statuses' => $statuses,
                'visibilities' => $visibilities,
            ])

            <div class="mt-6 flex justify-end gap-3">
                <x-button variant="ghost" size="lg" type="submit" x-data x-on:click="document.getElementById('status').value = 'draft'">Save draft</x-button>
                <x-button variant="primary" size="lg" type="submit">Update post</x-button>
            </div>
        </form>
    </div>
</x-admin-layout>
