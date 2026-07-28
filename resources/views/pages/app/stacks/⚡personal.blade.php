<?php

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    #[Computed]
    public function tools(): Collection
    {
        return auth()->user()->stack?->tools()->get() ?? new Collection;
    }
};
?>

<div class="flex flex-col">
    <x-domain.app.topbar>
        <p class="truncate text-sm font-medium text-foreground">My stack</p>
    </x-domain.app.topbar>

    <div class="flex flex-col gap-6 px-8 py-8">
        <x-domain.app.tool-list
            :tools="$this->tools"
            empty-icon="computer-terminal-01"
            empty-title="Your stack is empty"
            empty-description="Save tools you want to keep track of, and they'll show up here."
        />
    </div>
</div>
