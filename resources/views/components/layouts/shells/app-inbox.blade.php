@props(['title' => null])

@php
    $hasNotification = (bool) request()->route('notification');
@endphp

<x-layouts.root :title="$title">
    <div class="flex h-dvh w-full lg:gap-2 lg:p-2" x-data="{ sidebarOpen: false }">
        <div
            x-show="sidebarOpen"
            x-cloak
            x-transition.opacity
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/50 lg:hidden"
        ></div>

        <x-domain.app.sidebar/>

        <main class="flex flex-1 overflow-hidden bg-pane lg:rounded-sm lg:border lg:border-border">
            <div class="{{ $hasNotification ? 'hidden' : 'flex' }} w-full shrink-0 flex-col overflow-y-auto border-r border-border lg:flex lg:w-80">
                {{ $list }}
            </div>

            <div class="{{ $hasNotification ? 'flex' : 'hidden' }} min-w-0 flex-1 flex-col overflow-y-auto lg:flex">
                {{ $slot }}
            </div>
        </main>
    </div>
</x-layouts.root>
