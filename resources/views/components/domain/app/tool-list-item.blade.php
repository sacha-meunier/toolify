@props([
    'tool',
])

@if ($tool->trashed())
    <div class="flex items-center gap-3 px-6 py-3.5 opacity-60">
        <div class="flex min-w-0 flex-1 flex-col items-start gap-2">
            <p class="truncate text-sm font-medium text-foreground">{{ $tool->name }}</p>
            <x-ui.badge>Deleted from Toolify</x-ui.badge>
        </div>
    </div>
@else
    <div class="flex items-center gap-3 px-6 py-3.5 hover:bg-muted">
        <a href="{{ route('tools.show', $tool) }}" wire:navigate class="flex min-w-0 flex-1 flex-col gap-2">
            <div class="flex items-center gap-2">
                <p class="truncate text-sm font-medium text-foreground">{{ $tool->name }}</p>
                <p class="truncate text-sm text-muted-foreground">{{ $tool->tagline }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-1.5">
                @foreach ($tool->categories as $category)
                    <x-ui.badge>{{ $category->label() }}</x-ui.badge>
                @endforeach
                <x-ui.badge>{{ $tool->pricing->label() }}</x-ui.badge>
            </div>
        </a>

        <x-ui.button variant="secondary" size="sm" :href="$tool->website_url" target="_blank" label="Visit"/>
    </div>
@endif
