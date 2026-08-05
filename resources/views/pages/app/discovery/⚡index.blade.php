<?php

use App\Models\Tool;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    #[Computed]
    public function tools(): Collection
    {
        return Tool::query()->visibleTo(auth()->user())->orderBy('updated_at', 'desc')->get();
    }
};
?>

<div class="flex flex-col">
    <x-domain.app.topbar>
        <p class="truncate text-sm text-muted-foreground">Discovery</p>
    </x-domain.app.topbar>

    <div class="grid grid-cols-1 gap-6 px-8 py-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-2">
        @foreach ($this->tools as $tool)
            <a
                href="{{ route('tools.show', $tool) }}"
                wire:navigate
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
</div>
