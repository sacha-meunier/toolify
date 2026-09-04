@php
    $footerLinks = [
        'public.discover' => __('public/home.footer_link_discover'),
        'public.features' => __('public/home.footer_link_features'),
        'public.contact' => __('public/home.footer_link_contact'),
    ];
@endphp

<footer class="border-t border-border">
    <div class="mx-auto flex max-w-6xl flex-col items-center gap-4 px-6 py-10 sm:flex-row sm:justify-between lg:px-8">
        <a href="{{ route(auth()->check() ? 'public.homepage' : 'home') }}" class="flex items-center gap-2 rounded-md extend-touch-target">
            <x-ui.logo class="size-5 text-foreground"/>
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
