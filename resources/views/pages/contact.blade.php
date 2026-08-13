<x-app-layout>
    <x-slot name="title">{{ __('Contact') }}</x-slot>
    <x-slot name="metaDescription">{{ __('Get in touch with our team — we would love to hear from you.') }}</x-slot>

    <x-page-header
        eyebrow="Contact"
        :title="__('Get in touch')"
        :description="__('Have a question, suggestion or story idea? Send us a message.')"
    />

    <section>
        <x-container class="py-12 md:py-16">
            <div class="grid gap-10 lg:grid-cols-5">
                <div class="lg:col-span-2">
                    <h2 class="font-serif text-xl font-semibold tracking-tight text-content">Contact information</h2>
                    <p class="mt-3 text-sm leading-relaxed text-muted">
                        We usually reply within one business day. For partnerships and press enquiries,
                        use the email address below.
                    </p>

                    <div class="mt-6 space-y-4">
                        <a href="mailto:{{ config('blog.contact_recipient') ?? config('mail.from.address') }}" class="card card-hover flex items-center gap-4 p-4">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-primary-soft text-primary">
                                <x-icon icon="mail" class="h-5 w-5" />
                            </span>
                            <span class="min-w-0">
                                <span class="block text-xs font-semibold uppercase tracking-wider text-muted">Email</span>
                                <span class="block truncate text-sm font-medium text-content">
                                    {{ config('blog.contact_recipient') ?? config('mail.from.address') }}
                                </span>
                            </span>
                        </a>

                        <div class="card flex items-center gap-4 p-4">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-primary-soft text-primary">
                                <x-icon icon="map-pin" class="h-5 w-5" />
                            </span>
                            <span class="min-w-0">
                                <span class="block text-xs font-semibold uppercase tracking-wider text-muted">Based in</span>
                                <span class="block text-sm font-medium text-content">The world wide web</span>
                            </span>
                        </div>
                    </div>

                    <div class="card mt-6 bg-primary-soft/50 p-5">
                        <p class="text-xs font-semibold uppercase tracking-wider text-primary">Writing for us</p>
                        <p class="mt-2 text-sm leading-relaxed text-muted">
                            We are always open to pitches from writers. Tell us a little about the story
                            and why it belongs on {{ config('app.name') }}.
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <div class="card p-6 sm:p-8">
                        @if (session('status'))
                            <x-alert type="success" :dismissible="true" class="mb-6">{{ session('status') }}</x-alert>
                        @endif

                        <h2 class="font-serif text-xl font-semibold tracking-tight text-content">Send a message</h2>
                        <p class="mt-2 text-sm text-muted">Fields marked with * are required.</p>

                        <form method="POST" action="{{ route('contact.submit') }}" class="mt-6 space-y-5">
                            @csrf

                            <div class="grid gap-5 sm:grid-cols-2">
                                <x-input label="Your name *" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="Jane Doe" />
                                <x-input label="Your email *" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" placeholder="jane@example.com" />
                            </div>

                            <x-input label="Subject *" name="subject" value="{{ old('subject') }}" required placeholder="What is this about?" />

                            <x-textarea label="Message *" name="message" rows="6" required placeholder="Write your message…" />

                            <div class="flex flex-wrap items-center justify-between gap-4 pt-2">
                                <p class="text-xs text-muted">
                                    This site is protected by rate limiting. Please wait before sending another message.
                                </p>
                                <x-button type="submit" variant="primary" size="lg">Send message</x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </x-container>
    </section>
</x-app-layout>
