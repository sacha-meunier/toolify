<x-layouts.root>
    {{-- TODO sidebar-app --}}

    <div class="flex flex-1">
        <div class="flex flex-col w-80 border-r">
            {{-- TODO topbar-app --}}

            {{ $list }}
        </div>

        <div class="flex flex-col flex-1">
            {{-- TODO topbar-app-inbox --}}

            {{ $slot }}
        </div>
    </div>
</x-layouts.root>
