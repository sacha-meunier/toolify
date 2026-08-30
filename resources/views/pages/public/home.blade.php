@php
    $testimonials = [
        ['quote' => __('public/home.testimonial_one_quote'), 'author' => __('public/home.testimonial_one_author')],
        ['quote' => __('public/home.testimonial_two_quote'), 'author' => __('public/home.testimonial_two_author')],
    ];
@endphp

<x-layouts.shells.public>
    <x-seo.organization/>

    {{-- Hero --}}
    <section class="mx-auto max-w-6xl px-6 pt-10 pb-12 sm:pt-24 sm:pb-28 lg:px-8" aria-labelledby="hero-heading">
        <div class="mx-auto max-w-2xl text-center">
            <h1 id="hero-heading" class="text-4xl font-semibold tracking-tight text-foreground text-balance sm:text-6xl">
                {{ __('public/home.hero_heading') }}
            </h1>
            <p class="mt-6 text-lg text-muted-foreground">
                {{ __('public/home.hero_subheading') }}
            </p>
            <div class="mt-10 flex items-center justify-center gap-3">
                <x-ui.button variant="primary" size="lg" :href="route('register')" :label="__('public/home.cta_get_started')" icon:trailing="arrow-up-right-01"/>
                <x-ui.button variant="secondary" size="lg" :href="route('public.discover')" :label="__('public/home.cta_browse_tools')"/>
            </div>
        </div>

        <div class="mx-auto mt-8 max-w-5xl sm:mt-16">
            <div class="w-full overflow-clip rounded-xl border border-border bg-muted shadow-xs">
                <img src="{{ asset('images/marketing/discovery.png') }}" alt="" class="w-full object-cover">
            </div>
        </div>
    </section>

    {{-- Feature strip --}}
    <section class="border-y border-border bg-muted/40" aria-labelledby="feature-strip-heading">
        <h2 id="feature-strip-heading" class="sr-only">{{ __('public/home.feature_strip_heading') }}</h2>

        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-center gap-x-5 gap-y-3 px-6 py-8 text-sm font-medium text-muted-foreground lg:px-8">
            <span>{{ __('public/home.feature_strip_search') }}</span>
            <span>·</span>
            <span>{{ __('public/home.feature_strip_survey') }}</span>
            <span>·</span>
            <span>{{ __('public/home.feature_strip_stack') }}</span>
            <span>·</span>
            <span>{{ __('public/home.feature_strip_teams') }}</span>
            <span>·</span>
            <span>{{ __('public/home.feature_strip_listings') }}</span>
        </div>
    </section>

    <x-domain.marketing.feature-section
        :title="__('public/home.feature_one_title')"
        :description="__('public/home.feature_one_description')"
    >
        <x-slot:media>
            <img src="{{ asset('images/marketing/search-filters.png') }}" alt="" class="w-full object-cover">
        </x-slot:media>
    </x-domain.marketing.feature-section>

    <x-domain.marketing.feature-section
        :title="__('public/home.feature_two_title')"
        :description="__('public/home.feature_two_description')"
        :reverse="true"
    >
        <x-slot:media>
            <img src="{{ asset('images/marketing/edit-survey.png') }}" alt="" class="w-full object-cover">
        </x-slot:media>
    </x-domain.marketing.feature-section>

    <x-domain.marketing.feature-section
        :title="__('public/home.feature_three_title')"
        :description="__('public/home.feature_three_description')"
    >
        <x-slot:media>
            <img src="{{ asset('images/marketing/stack.png') }}" alt="" class="w-full object-cover">
        </x-slot:media>
    </x-domain.marketing.feature-section>

    {{-- Workspaces & Listings --}}
    <section class="mx-auto max-w-6xl px-6 py-12 sm:py-24 lg:px-9" aria-labelledby="grid-section-heading">
        <h2 id="grid-section-heading" class="sr-only">{{ __('public/home.grid_section_heading') }}</h2>

        <div class="grid gap-6 sm:grid-cols-2">
            <div class="flex flex-col gap-2 rounded-xl border border-border bg-card p-6 shadow-xs">
                <h3 class="text-lg font-semibold text-foreground">{{ __('public/home.grid_teams_title') }}</h3>
                <p class="text-muted-foreground">{{ __('public/home.grid_teams_description') }}</p>
            </div>

            <div class="flex flex-col gap-2 rounded-xl border border-border bg-card p-6 shadow-xs">
                <h3 class="text-lg font-semibold text-foreground">{{ __('public/home.grid_listings_title') }}</h3>
                <p class="text-muted-foreground">{{ __('public/home.grid_listings_description') }}</p>
            </div>
        </div>
    </section>

    {{-- Final CTA --}}
    <section class="border-y border-border bg-muted/40" aria-labelledby="final-cta-heading">
        <div class="mx-auto max-w-6xl px-6 py-12 text-center sm:py-24 lg:px-8">
            <h2 id="final-cta-heading" class="text-3xl font-semibold tracking-tight text-foreground">{{ __('public/home.final_cta_heading') }}</h2>
            <div class="mt-8">
                <x-ui.button variant="primary" size="lg" :href="route('register')" :label="__('public/home.cta_get_started')" icon:trailing="arrow-up-right-01"/>
            </div>
            <p class="mt-4 text-sm text-muted-foreground">{{ __('public/home.final_cta_note') }}</p>
        </div>
    </section>

    <x-domain.marketing.footer/>
</x-layouts.shells.public>
