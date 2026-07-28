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
        return Tool::query()->orderBy('updated_at', 'desc')->get();
    }
};
?>

<div class="flex flex-col">
    <x-domain.app.topbar>
        <p class="truncate text-sm text-muted-foreground">Discovery</p>
    </x-domain.app.topbar>

    <div class="grid grid-cols-1 gap-6 px-8 py-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">
        @foreach ($this->tools as $tool)
            <a
                href="{{ route('tools.show', $tool) }}"
                wire:navigate
                class="flex flex-col overflow-hidden rounded-xl border border-border bg-card shadow-xs transition-colors hover:border-foreground/20 focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-ring/50"
            >
                <div class="flex flex-col gap-1 p-4">
                    <p class="text-base font-medium text-card-foreground">{{ $tool->name }}</p>
                    <p class="truncate text-sm text-muted-foreground">{{ $tool->tagline }}</p>
                </div>

                <div class="px-8">
                    <div class="h-[225px] w-full rounded-t-md bg-muted"></div>
                </div>
            </a>
        @endforeach
    </div>
</div>
