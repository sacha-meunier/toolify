<x-layouts.shells.public>
    <x-seo.tool-list :tools="$tools"/>

    <div class="mx-auto max-w-6xl px-6 py-16 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <h1 class="text-4xl font-semibold tracking-tight text-foreground">{{ __('public/discover.title') }}</h1>
            <p class="mt-4 text-lg text-muted-foreground">{{ __('public/discover.description') }}</p>
        </div>

        <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($tools as $tool)
                <a
                    href="{{ route('public.tools.show', $tool) }}"
                    class="flex flex-col gap-4 overflow-hidden rounded-xl border border-border bg-card shadow-xs transition-colors hover:border-foreground/20 focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-ring/50"
                >
                    <div class="flex gap-3 p-4">
                        <div class="self-center flex size-10 shrink-0 items-center justify-center rounded-xl text-sidebar-primary-foreground">
                            @if ($tool->logo_url)
                                <img src="{{ $tool->logo_url }}" alt="" class="size-full rounded-lg object-cover">
                            @else
                                <x-ui.icon.command class="size-5"/>
                            @endif
                        </div>

                        <div class="flex min-w-0 flex-1 flex-col">
                            <p class="text-base font-medium text-card-foreground">{{ $tool->name }}</p>
                            <p class="truncate text-sm text-muted-foreground">{{ $tool->tagline }}</p>
                        </div>
                    </div>

                    <div class="px-12">
                        @if ($tool->banner_url)
                            <img src="{{ $tool->banner_url }}" alt="" class="h-[225px] w-full rounded-t-md object-cover">
                        @else
                            <div class="h-[225px] w-full rounded-t-md bg-muted"></div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $tools->links() }}
        </div>
    </div>
</x-layouts.shells.public>
