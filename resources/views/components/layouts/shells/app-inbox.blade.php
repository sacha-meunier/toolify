<x-layouts.root>
    <div class="flex h-dvh w-full gap-2 p-2">
        <x-domain.app.sidebar/>

        <main class="flex flex-1 overflow-hidden rounded-sm border border-border bg-background">
            <div class="flex w-80 shrink-0 flex-col overflow-y-auto border-r border-border">
                {{ $list }}
            </div>

            <div class="flex min-w-0 flex-1 flex-col overflow-y-auto">
                {{ $slot }}
            </div>
        </main>
    </div>
</x-layouts.root>
