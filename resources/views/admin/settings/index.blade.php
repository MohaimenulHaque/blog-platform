<x-admin-layout>
    <x-slot name="title">{{ __('Settings') }}</x-slot>

    @php
        $activeTab = request()->query('tab', 'general');
        $activeLabel = $groups[$activeTab]['label'] ?? ucfirst($activeTab);
        $activeSettings = collect($groups[$activeTab]['settings'] ?? []);
    @endphp

    <div class="space-y-6">
        <div>
            <h1 class="font-serif text-2xl font-semibold tracking-tight text-content">Settings</h1>
            <p class="mt-0.5 text-sm text-muted">Configure the blog's identity, branding, social profiles and SEO.</p>
        </div>

        @if (session('status'))
            <x-alert type="success" :dismissible="true">{{ session('status') }}</x-alert>
        @endif

        <x-card>
            <div class="flex flex-col gap-1 border-b border-line p-2 sm:flex-row sm:flex-wrap">
                @foreach ($groups as $key => $group)
                    <a
                        href="{{ route('admin.settings.index', ['tab' => $key]) }}"
                        class="{{ $activeTab === $key ? 'rounded-xl bg-primary-soft px-4 py-2 text-sm font-medium text-primary' : 'rounded-xl px-4 py-2 text-sm font-medium text-muted transition-colors hover:bg-surface-alt hover:text-content' }}"
                    >
                        {{ $group['label'] }}
                    </a>
                @endforeach
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PATCH')

                <input type="hidden" name="group" value="{{ $activeTab }}">

                <div class="mb-6">
                    <h2 class="font-serif text-lg font-semibold text-content">{{ $activeLabel }}</h2>
                    <p class="mt-0.5 text-sm text-muted">These values appear across the site.</p>
                </div>

                <div class="space-y-5">
                    @foreach ($activeSettings as $meta)
                        @php
                            $name = 'settings['.str_replace('.', '][', $meta['key']).']';
                            $value = $meta['value'] ?? '';
                        @endphp

                        @if ($meta['type'] === 'image')
                            <div class="rounded-2xl border border-line p-4">
                                <label for="{{ $meta['key'] }}" class="label">{{ $meta['label'] }}</label>
                                <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-center">
                                    <div class="grid h-24 w-36 shrink-0 place-items-center overflow-hidden rounded-xl border border-line bg-surface-alt">
                                        @if ($value && \Illuminate\Support\Str::startsWith($value, 'settings/'))
                                            <img src="{{ asset('storage/'.$value) }}" alt="{{ $meta['label'] }}" class="h-full w-full object-cover">
                                        @else
                                            <span class="text-xs text-muted">No image</span>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <input
                                            id="{{ $meta['key'] }}"
                                            type="file"
                                            name="{{ $name }}"
                                            accept="image/*"
                                            class="block w-full text-sm text-content-soft file:mr-4 file:rounded-lg file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-primary-hover"
                                        >
                                        <p class="mt-2 text-xs text-muted">Replace the current {{ $meta['label'] }}. Leave empty to keep it.</p>
                                        <x-input-error :messages="$errors->get('settings.'.$meta['key'])" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        @elseif ($meta['type'] === 'textarea')
                            <div>
                                <label for="{{ $meta['key'] }}" class="label">{{ $meta['label'] }}</label>
                                <textarea id="{{ $meta['key'] }}" name="{{ $name }}" rows="4" class="input-field resize-y">{{ old($name, $value) }}</textarea>
                                <x-input-error :messages="$errors->get('settings.'.$meta['key'])" class="mt-2" />
                            </div>
                        @else
                            <div>
                                <label for="{{ $meta['key'] }}" class="label">{{ $meta['label'] }}</label>
                                <input
                                    id="{{ $meta['key'] }}"
                                    type="{{ $meta['type'] }}"
                                    name="{{ $name }}"
                                    value="{{ old($name, $value) }}"
                                    class="input-field"
                                >
                                <x-input-error :messages="$errors->get('settings.'.$meta['key'])" class="mt-2" />
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="mt-8 flex justify-end">
                    <x-button variant="primary" size="md" type="submit">Save settings</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-admin-layout>
