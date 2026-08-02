<x-layouts.root>
    <div class="flex p-2 gap-2 h-dvh w-full">
        <x-domain.app.sidebar.settings/>

        <main class="flex flex-1 flex-col rounded-sm border border-border bg-background overflow-y-auto">
            {{ $slot }}
        </main>
    </div>
</x-layouts.root>
