@props(['title' => null])

<x-layouts.root :title="$title">
    <div class="flex h-dvh w-full lg:gap-2 lg:p-2" x-data="{ sidebarOpen: false }">
        <div
            x-show="sidebarOpen"
            x-cloak
            x-transition.opacity
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/50 lg:hidden"
        ></div>

        <x-domain.app.sidebar.settings/>

        <main class="flex flex-1 flex-col overflow-y-auto bg-pane lg:rounded-sm lg:border lg:border-border">
            {{ $slot }}
        </main>
    </div>
</x-layouts.root>
