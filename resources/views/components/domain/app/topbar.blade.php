@props([])

<div {{ $attributes->class('flex flex-col gap-2 border-b border-border px-4 py-2.5 lg:h-14 lg:flex-row lg:items-center lg:justify-between lg:gap-4 lg:px-8 lg:py-0') }}>
    <div class="flex min-w-0 items-center gap-2 lg:flex-1">
        <x-ui.button
            variant="ghost"
            size="icon-xs"
            icon="sidebar-left"
            :aria-label="__('app/components/topbar.toggle_sidebar')"
            class="shrink-0 lg:hidden"
            @click="sidebarOpen = true"
        />

        {{ $slot }}
    </div>

    @isset($actions)
        <div class="flex shrink-0 items-center justify-start gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>
