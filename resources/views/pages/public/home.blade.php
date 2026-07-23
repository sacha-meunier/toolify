@php
    $footerLinks = [
        'public.discover' => 'Discover',
        'public.features' => 'Features',
        'public.contact' => 'Contact',
    ];

    $testimonials = [
        ['quote' => 'We cut our tool audit time in half.', 'author' => 'Camille Roy, CTO @ Novatik'],
        ['quote' => 'Finally a reason to stop the spreadsheet.', 'author' => 'Julien Marchand, Lead Dev @ Kaskad'],
    ];
@endphp

<x-layouts.shells.public>
    {{-- Hero --}}
    <section class="mx-auto max-w-6xl px-6 pt-16 pb-20 sm:pt-24 sm:pb-28 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h1 class="text-4xl font-semibold tracking-tight text-foreground sm:text-6xl">
                Your tech stack, always up to date.
            </h1>
            <p class="mt-6 text-lg text-muted-foreground">
                Track tools, get alerted on updates, and share your watch with your team.
            </p>
            <div class="mt-10 flex items-center justify-center gap-3">
                <x-ui.button variant="primary" size="lg" :href="route('register')" label="Get started free" icon:trailing="circle-arrow-left-02"/>
                <x-ui.button variant="secondary" size="lg" :href="route('public.discover')" label="Browse tools"/>
            </div>
        </div>

        <div class="mx-auto mt-16 max-w-5xl">
            <div class="aspect-video w-full rounded-xl border border-border bg-muted shadow-xs"></div>
        </div>
    </section>

    {{-- Feature strip --}}
    <section class="border-y border-border bg-muted/40">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-center gap-x-10 gap-y-3 px-6 py-8 text-sm font-medium text-muted-foreground lg:px-8">
            <span>&#10022; Track</span>
            <span>&#10022; Alert</span>
            <span>&#10022; Collaborate</span>
            <span>&#10022; Ship</span>
        </div>
    </section>

    <x-domain.marketing.feature-section
        title="Stop missing updates."
        description="Toolify tracks the tools in your stack and tells you when something changes — new version, pricing shift, new feature."
    />

    <x-domain.marketing.feature-section
        title="Build your own watch."
        description="Create views that filter the catalog by category, tags, pricing model, or any combination. Save them, share them."
        :reverse="true"
    />

    <x-domain.marketing.feature-section
        title="Your team's radar, not just yours."
        description="Share views with your team. See what everyone's been exploring. Stay aligned without the Slack threads."
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
        <h2 class="text-3xl font-semibold tracking-tight text-foreground">Start watching your stack today.</h2>
        <div class="mt-8">
            <x-ui.button variant="primary" size="lg" :href="route('register')" label="Get started free" icon:trailing="circle-arrow-left-02"/>
        </div>
        <p class="mt-4 text-sm text-muted-foreground">No credit card required &middot; Free plan available</p>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-border">
        <div class="mx-auto flex max-w-6xl flex-col items-center gap-4 px-6 py-10 sm:flex-row sm:justify-between lg:px-8">
            <a href="{{ route(auth()->check() ? 'public.homepage' : 'home') }}" class="flex items-center gap-2 rounded-md hover:bg-muted extend-touch-target">
                <span class="flex size-5 items-center justify-center rounded-md bg-sidebar-primary">
                    <x-ui.icon.command size="xs" stroke-width="1.5" class="size-3 text-sidebar-primary-foreground"/>
                </span>
                <span class="text-sm font-semibold text-foreground">Toolify</span>
            </a>

            <div class="flex items-center gap-1">
                @foreach ($footerLinks as $route => $label)
                    <x-ui.button variant="ghost" size="sm" :href="route($route)" :label="$label"/>
                @endforeach
            </div>

            <p class="text-sm text-muted-foreground">&copy; {{ now()->year }} Toolify</p>
        </div>
    </footer>
</x-layouts.shells.public>
