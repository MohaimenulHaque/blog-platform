@php
    $old = fn (string $key, $default = null) => old($key, $author?->{$key} ?? $default);
    $links = old('social_links', $author?->social_links ?? []);
@endphp

<div class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
        <x-card class="space-y-5 p-5">
            <div class="flex items-center gap-4">
                <x-avatar :user="$author" size="xl" class="ring-4 ring-surface" />
                <div>
                    <label class="label mb-0">Avatar</label>
                    <label class="btn btn-outline btn-sm mt-1 cursor-pointer">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" /><circle cx="8.5" cy="8.5" r="1.5" /><path d="M21 15l-5-5L5 21" /></svg>
                        Change avatar
                        <input type="file" name="avatar" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" class="hidden">
                    </label>
                </div>
            </div>
            <x-input-error :messages="$errors->get('avatar')" class="mt-1" />

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="name" class="label">Full name <span class="text-danger">*</span></label>
                    <input id="name" type="text" name="name" value="{{ $old('name') }}" required @class(['input-field', '!border-danger focus:!ring-danger' => $errors->has('name')])>
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <div>
                    <label for="username" class="label">Username <span class="text-danger">*</span></label>
                    <input id="username" type="text" name="username" value="{{ $old('username') }}" required @class(['input-field', '!border-danger focus:!ring-danger' => $errors->has('username')])>
                    <x-input-error :messages="$errors->get('username')" class="mt-1" />
                </div>

                <div>
                    <label for="email" class="label">Email <span class="text-danger">*</span></label>
                    <input id="email" type="email" name="email" value="{{ $old('email') }}" required @class(['input-field', '!border-danger focus:!ring-danger' => $errors->has('email')])>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div>
                    <label for="password" class="label">Password @if ($author) <span class="font-normal text-muted">(leave blank to keep)</span> @else <span class="text-danger">*</span> @endif</label>
                    <input id="password" type="password" name="password" @if (! $author) required @endif @class(['input-field', '!border-danger focus:!ring-danger' => $errors->has('password')])>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div>
                    <label for="designation" class="label">Designation</label>
                    <input id="designation" type="text" name="designation" value="{{ $old('designation') }}" placeholder="e.g. Senior Staff Writer" class="input-field">
                    <x-input-error :messages="$errors->get('designation')" class="mt-1" />
                </div>

                <div>
                    <label for="website" class="label">Website</label>
                    <input id="website" type="url" name="website" value="{{ $old('website') }}" placeholder="https://example.com" class="input-field">
                    <x-input-error :messages="$errors->get('website')" class="mt-1" />
                </div>
            </div>

            <div>
                <label for="bio" class="label">Bio</label>
                <textarea id="bio" name="bio" rows="4" placeholder="A short introduction shown on the author's profile…" @class(['input-field resize-y', '!border-danger focus:!ring-danger' => $errors->has('bio')])>{{ $old('bio') }}</textarea>
                <x-input-error :messages="$errors->get('bio')" class="mt-1" />
            </div>
        </x-card>
    </div>

    <div class="space-y-6">
        <x-card class="p-5">
            <h2 class="mb-4 font-serif text-sm font-semibold text-content">Social links</h2>

            <div class="space-y-4">
                @foreach ($socialPlatforms as $platform)
                    <div>
                        <label for="social_{{ $platform }}" class="label capitalize">{{ $platform }}</label>
                        <input
                            id="social_{{ $platform }}"
                            type="url"
                            name="social_links[{{ $platform }}]"
                            value="{{ $links[$platform] ?? '' }}"
                            placeholder="https://…"
                            class="input-field"
                        >
                    </div>
                @endforeach

                <x-input-error :messages="$errors->get('social_links')" class="mt-1" />
            </div>
        </x-card>
    </div>
</div>
