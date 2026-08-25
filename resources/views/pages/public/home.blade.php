@php
    $footerLinks = [
        'public.discover' => __('public/home.footer_link_discover'),
        'public.features' => __('public/home.footer_link_features'),
        'public.contact' => __('public/home.footer_link_contact'),
    ];

    $testimonials = [
        ['quote' => __('public/home.testimonial_one_quote'), 'author' => __('public/home.testimonial_one_author')],
        ['quote' => __('public/home.testimonial_two_quote'), 'author' => __('public/home.testimonial_two_author')],
    ];
@endphp

<x-layouts.shells.public>
    {{-- Hero --}}
    <section class="mx-auto max-w-6xl px-6 pt-16 pb-20 sm:pt-24 sm:pb-28 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h1 class="text-4xl font-semibold tracking-tight text-foreground sm:text-6xl">
                {{ __('public/home.hero_heading') }}
            </h1>
            <p class="mt-6 text-lg text-muted-foreground">
                {{ __('public/home.hero_subheading') }}
            </p>
            <div class="mt-10 flex items-center justify-center gap-3">
                <x-ui.button variant="primary" size="lg" :href="route('register')" :label="__('public/home.cta_get_started')" icon:trailing="circle-arrow-left-02"/>
                <x-ui.button variant="secondary" size="lg" :href="route('public.discover')" :label="__('public/home.cta_browse_tools')"/>
            </div>
        </div>

        <div class="mx-auto mt-16 max-w-5xl">
            <div class="aspect-video w-full rounded-xl border border-border bg-muted shadow-xs"></div>
        </div>
    </section>

    {{-- Feature strip --}}
    <section class="border-y border-border bg-muted/40">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-center gap-x-10 gap-y-3 px-6 py-8 text-sm font-medium text-muted-foreground lg:px-8">
            <span>&#10022; {{ __('public/home.feature_strip_track') }}</span>
            <span>&#10022; {{ __('public/home.feature_strip_alert') }}</span>
            <span>&#10022; {{ __('public/home.feature_strip_collaborate') }}</span>
            <span>&#10022; {{ __('public/home.feature_strip_ship') }}</span>
        </div>
    </section>

    <x-domain.marketing.feature-section
        :title="__('public/home.feature_one_title')"
        :description="__('public/home.feature_one_description')"
    />

    <x-domain.marketing.feature-section
        :title="__('public/home.feature_two_title')"
        :description="__('public/home.feature_two_description')"
        :reverse="true"
    />

    <x-domain.marketing.feature-section
        :title="__('public/home.feature_three_title')"
        :description="__('public/home.feature_three_description')"
    />

    {{-- Social proof --}}
    <section class="border-y border-border bg-muted/40">
        <div class="mx-auto grid max-w-6xl gap-6 px-6 py-20 sm:grid-cols-2 lg:px-8">
            @foreach ($testimonials as $testimonial)
                <x-domain.marketing.testimonial :quote="$testimonial['quote']" :author="$testimonial['author']"/>
            @endforeach
        </div>
    </section>

    {{-- Final CTA --}}
    <section class="mx-auto max-w-6xl px-6 py-24 text-center lg:px-8">
        <h2 class="text-3xl font-semibold tracking-tight text-foreground">{{ __('public/home.final_cta_heading') }}</h2>
        <div class="mt-8">
            <x-ui.button variant="primary" size="lg" :href="route('register')" :label="__('public/home.cta_get_started')" icon:trailing="circle-arrow-left-02"/>
        </div>
        <p class="mt-4 text-sm text-muted-foreground">{{ __('public/home.final_cta_note') }}</p>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-border">
        <div class="mx-auto flex max-w-6xl flex-col items-center gap-4 px-6 py-10 sm:flex-row sm:justify-between lg:px-8">
            <a href="{{ route(auth()->check() ? 'public.homepage' : 'home') }}" class="flex items-center gap-2 rounded-md hover:bg-muted extend-touch-target">
                <span class="flex size-5 items-center justify-center rounded-md bg-sidebar-primary">
                    <x-ui.icon.command size="xs" stroke-width="1.5" class="size-3 text-sidebar-primary-foreground"/>
                </span>
                <span class="text-sm font-semibold text-foreground">{{ __('public/home.brand_name') }}</span>
            </a>

            <div class="flex items-center gap-1">
                @foreach ($footerLinks as $route => $label)
                    <x-ui.button variant="ghost" size="sm" :href="route($route)" :label="$label"/>
                @endforeach
            </div>

            <p class="text-sm text-muted-foreground">{{ __('public/home.footer_copyright', ['year' => now()->year]) }}</p>
        </div>
    </footer>
</x-layouts.shells.public>
