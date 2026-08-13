<x-app-layout>
    <x-slot name="title">{{ __('About') }}</x-slot>

    <x-page-header
        eyebrow="Our story"
        :title="__('A journal for the curious')"
        :description="__('BlogPlatform began with a simple idea: the internet deserves writing that is thoughtful, elegant and easy to read.')"
    />

    <section>
        <x-container class="py-16 md:py-20">
            <div class="grid gap-12 lg:grid-cols-2">
                <div class="prose max-w-none">
                    <h2 class="section-title">What we believe</h2>
                    <p class="mt-4 leading-relaxed text-muted">
                        We believe attention is the scarcest resource of our time. Every story we publish is
                        edited with care, written with depth, and presented in a reading experience built
                        around calm and clarity.
                    </p>
                    <p class="mt-3 leading-relaxed text-muted">
                        We are writers, designers and engineers building the editorial home we wish existed —
                        one where long-form ideas can thrive again.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-card padded>
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-primary-soft text-primary">
                            <x-icon icon="sparkles" class="h-5 w-5" />
                        </span>
                        <h3 class="mt-4 font-serif text-lg font-semibold text-content">Editorial quality</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted">Every piece is reviewed, refined and designed to be worth your time.</p>
                    </x-card>

                    <x-card padded>
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-secondary-soft text-secondary">
                            <x-icon icon="users" class="h-5 w-5" />
                        </span>
                        <h3 class="mt-4 font-serif text-lg font-semibold text-content">Community first</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted">Built with readers and writers, not around advertising metrics.</p>
                    </x-card>

                    <x-card padded>
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-success-soft text-success">
                            <x-icon icon="pen" class="h-5 w-5" />
                        </span>
                        <h3 class="mt-4 font-serif text-lg font-semibold text-content">Craft over speed</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted">We publish less, on purpose, so every story can be its best self.</p>
                    </x-card>

                    <x-card padded>
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-info-soft text-info">
                            <x-icon icon="moon" class="h-5 w-5" />
                        </span>
                        <h3 class="mt-4 font-serif text-lg font-semibold text-content">A beautiful web</h3>
                        <p class="mt-2 text-sm leading-relaxed text-muted">Fast, accessible and gorgeous in light and dark — the web we want back.</p>
                    </x-card>
                </div>
            </div>
        </x-container>
    </section>
</x-app-layout>
