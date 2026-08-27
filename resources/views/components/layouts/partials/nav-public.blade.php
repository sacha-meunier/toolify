@php
    $links = [
        'public.discover' => __('layout.nav_discover'),
        'public.features' => __('layout.nav_features'),
        'public.contact' => __('layout.nav_contact'),
    ];
@endphp

<div x-data="{ mobileMenuOpen: false }" class="fixed inset-x-0 top-0 z-40 border-b border-border bg-background">
    <nav class="mx-auto flex h-14 max-w-6xl items-center justify-between pl-2.5 pr-4 lg:px-8">
        <a href="{{ route(auth()->check() ? 'public.homepage' : 'home') }}" class="flex shrink-0 items-center gap-2 py-2 rounded-md extend-touch-target">
            <span class="flex size-5 items-center justify-center rounded-md bg-sidebar-primary">
                <x-ui.icon.command size="xs" stroke-width="1.5" class="size-3 text-sidebar-primary-foreground"/>
            </span>
            <span class="text-sm font-semibold text-sidebar-foreground">{{ __('layout.brand_name') }}</span>
        </a>

        <div class="hidden items-center gap-1 lg:flex">
            @foreach ($links as $route => $label)
                <x-ui.button
                    variant="ghost"
                    size="lg"
                    :href="route($route)"
                    :label="$label"
                    :class="request()->routeIs($route) ? 'bg-muted text-foreground font-medium' : ''"
                />
            @endforeach
        </div>

        <div class="hidden items-center gap-2.5 lg:flex">
            <livewire:domain.marketing.dropdown.locale-switcher/>

            @auth
                <x-ui.button variant="primary" size="default" :href="route('discovery')" :label="__('layout.nav_open_app')"/>
            @else
                <x-ui.button variant="secondary" size="default" :href="route('login')" :label="__('layout.nav_log_in')"/>
                <x-ui.button variant="primary" size="default" :href="route('register')" :label="__('layout.nav_sign_up')"/>
            @endauth
        </div>

        <x-ui.button
            variant="outline"
            size="icon-xs"
            icon="menu-two-line"
            class="lg:hidden"
            @click="mobileMenuOpen = !mobileMenuOpen"
            :aria-label="__('layout.nav_toggle_menu')"
        />
    </nav>

    <div
        x-show="mobileMenuOpen"
        x-cloak
        @click.outside="mobileMenuOpen = false"
        class="absolute inset-x-0 top-full z-50 flex flex-col gap-1 border-b border-border bg-background p-4 shadow-xs lg:hidden"
    >
        @foreach ($links as $route => $label)
            <x-ui.button
                variant="ghost"
                size="lg"
                :href="route($route)"
                :label="$label"
                :class="'w-full justify-start '.(request()->routeIs($route) ? 'bg-muted text-foreground font-medium' : '')"
            />
        @endforeach

        <div class="mt-2 flex flex-col gap-2 border-t border-border pt-4">
            <livewire:domain.marketing.dropdown.locale-switcher/>

            @auth
                <x-ui.button variant="secondary" size="sm" class="w-full" :href="route('discovery')" :label="__('layout.nav_open_app')"/>
            @else
                <x-ui.button variant="ghost" size="sm" class="w-full" :href="route('login')" :label="__('layout.nav_log_in')"/>
                <x-ui.button variant="secondary" size="sm" class="w-full" :href="route('register')" :label="__('layout.nav_sign_up')"/>
            @endauth
        </div>
    </div>
</div>
