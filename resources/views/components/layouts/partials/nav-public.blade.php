@php
    $links = [
        'public.discover' => 'Discover',
        'public.features' => 'Features',
        'public.contact' => 'Contact',
    ];
@endphp

<div x-data="{ mobileMenuOpen: false }" class="fixed inset-x-0 top-0 z-40 border-b border-border bg-background">
    <nav class="mx-auto flex h-14 max-w-6xl items-center justify-between pl-2.5 pr-4 lg:px-8">
        <a href="{{ route(auth()->check() ? 'public.homepage' : 'home') }}" class="flex shrink-0 items-center gap-2 px-1.5 py-2 rounded-md hover:bg-muted extend-touch-target">
            <span class="flex size-5 items-center justify-center rounded-md bg-sidebar-primary">
                <x-ui.icon.command size="xs" stroke-width="1.5" class="size-3 text-sidebar-primary-foreground"/>
            </span>
            <span class="text-sm font-semibold text-sidebar-foreground">Toolify</span>
        </a>

        <div class="hidden items-center gap-1 lg:flex">
            @foreach ($links as $route => $label)
                <x-ui.button variant="ghost" size="lg" :href="route($route)" :label="$label"/>
            @endforeach
        </div>

        <div class="hidden items-center gap-2.5 lg:flex">
            @auth
                <x-ui.button variant="primary" size="default" :href="route('discovery')" label="Open app"/>
            @else
                <x-ui.button variant="secondary" size="default" :href="route('login')" label="Log in"/>
                <x-ui.button variant="primary" size="default" :href="route('register')" label="Sign up"/>
            @endauth
        </div>

        <x-ui.button
            variant="outline"
            size="icon-xs"
            icon="menu-two-line"
            class="lg:hidden"
            @click="mobileMenuOpen = !mobileMenuOpen"
            aria-label="Toggle menu"
        />
    </nav>

    <div
        x-show="mobileMenuOpen"
        x-cloak
        @click.outside="mobileMenuOpen = false"
        class="absolute inset-x-0 top-full z-50 flex flex-col gap-1 border-b border-border bg-background p-4 shadow-xs lg:hidden"
    >
        @foreach ($links as $route => $label)
            <x-ui.button variant="ghost" size="lg" class="w-full justify-start" :href="route($route)" :label="$label"/>
        @endforeach

        <div class="mt-2 flex flex-col gap-2 border-t border-border pt-4">
            @auth
                <x-ui.button variant="secondary" size="sm" class="w-full" :href="route('discovery')" label="Open app"/>
            @else
                <x-ui.button variant="ghost" size="sm" class="w-full" :href="route('login')" label="Log in"/>
                <x-ui.button variant="secondary" size="sm" class="w-full" :href="route('register')" label="Sign up"/>
            @endauth
        </div>
    </div>
</div>
