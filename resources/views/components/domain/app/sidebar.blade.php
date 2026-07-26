@props([])

<aside {{ $attributes->class('flex h-full w-61 shrink-0 flex-col overflow-clip rounded-sm border border-sidebar-border bg-sidebar') }}>
    <div class="flex h-14 shrink-0 items-center px-3">
        <x-ui.button
            variant="ghost"
            size="sm"
            icon="command"
            icon:trailing="arrow-down-01"
            class="gap-2 px-1.5 font-semibold text-sidebar-foreground"
            label="Toolify Inc"
        />
    </div>

    <nav class="flex min-h-0 flex-1 flex-col items-start overflow-y-auto px-1">
        <x-domain.app.sidebar.group>
            <x-domain.app.sidebar.item icon="search-01" :href="route('search')">Search</x-domain.app.sidebar.item>
            <x-domain.app.sidebar.item icon="discover-circle" :href="route('discovery')">Discovery</x-domain.app.sidebar.item>
            <x-domain.app.sidebar.item icon="inbox" href="#" badge="23">Inbox</x-domain.app.sidebar.item>
        </x-domain.app.sidebar.group>

        <x-domain.app.sidebar.group label="Personal">
            <x-domain.app.sidebar.item icon="computer-terminal-01" href="#">Stack</x-domain.app.sidebar.item>
            <x-domain.app.sidebar.item icon="layer" href="#">Views</x-domain.app.sidebar.item>
        </x-domain.app.sidebar.group>

        <x-domain.app.sidebar.group label="Workspace">
            <x-domain.app.sidebar.item icon="computer-terminal-01" disabled>Stack</x-domain.app.sidebar.item>
            <x-domain.app.sidebar.item icon="layer" disabled>Views</x-domain.app.sidebar.item>
        </x-domain.app.sidebar.group>
    </nav>

    <div class="flex shrink-0 items-center justify-between px-4 py-3.5">
        <div class="size-6 shrink-0 rounded-full border border-border bg-muted"></div>
        <x-ui.button variant="outline" size="icon-xs" label="?"/>
    </div>
</aside>
