@props([
    'tool',
])

@if ($tool->trashed())
    <div {{ $attributes->class('flex items-center gap-3 px-4 py-3.5 opacity-60 lg:gap-6 lg:px-6') }}>
        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl text-sidebar-primary-foreground">
            @if ($tool->logo_url)
                <img src="{{ $tool->logo_url }}" alt="" class="size-full rounded-lg object-cover">
            @else
                <x-ui.icon.command class="size-5"/>
            @endif
        </div>

        <div class="flex min-w-0 flex-1 flex-col items-start gap-2">
            <p class="w-full truncate text-sm font-medium text-foreground">{{ $tool->name }}</p>
            <x-ui.badge>{{ __('app/components/tool-list-item.deleted_badge') }}</x-ui.badge>
        </div>
    </div>
@else
    <div {{ $attributes->class('relative flex flex-wrap items-center gap-3 px-4 py-3.5 hover:bg-muted lg:flex-nowrap lg:px-6') }}>
        <a href="{{ route('tools.show', $tool) }}" wire:navigate class="flex min-w-0 flex-1 items-center gap-3 before:absolute before:inset-0 lg:gap-6">
            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl text-sidebar-primary-foreground">
                @if ($tool->logo_url)
                    <img src="{{ $tool->logo_url }}" alt="" class="size-full rounded-lg object-cover">
                @else
                    <x-ui.icon.command class="size-5"/>
                @endif
            </div>

            <div class="flex min-w-0 flex-1 flex-col gap-2">
                <div class="flex min-w-0 flex-col lg:flex-row lg:items-center lg:gap-2">
                    <p class="w-full min-w-0 truncate text-sm font-medium text-foreground">{{ $tool->name }}</p>
                    <p class="w-full min-w-0 truncate text-sm text-muted-foreground">{{ $tool->tagline }}</p>
                </div>

                <div class="flex flex-wrap items-center gap-1.5">
                    @foreach ($tool->categories as $category)
                        <x-ui.badge>{{ $category->label() }}</x-ui.badge>
                    @endforeach
                    <x-ui.badge>{{ $tool->pricing->label() }}</x-ui.badge>
                </div>
            </div>
        </a>

        <x-ui.button variant="secondary" size="sm" :href="$tool->website_url" target="_blank" :label="__('app/components/tool-list-item.visit')" class="w-full shrink-0 justify-center lg:w-auto"/>
    </div>
@endif
