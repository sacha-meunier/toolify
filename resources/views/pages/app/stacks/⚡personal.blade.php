<?php

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    #[Computed]
    public function tools(): Collection
    {
        return auth()->user()->stack?->tools()->withTrashed()->get() ?? new Collection;
    }
};
?>

<div class="flex flex-col">
    <x-domain.app.topbar>
        <p class="truncate text-sm font-medium text-foreground">{{ __('app/stacks/personal.title') }}</p>
    </x-domain.app.topbar>

    <div class="flex flex-col gap-6 px-8 py-8">
        <x-domain.app.tool-list
            :tools="$this->tools"
            empty-icon="layer"
            :empty-title="__('app/stacks/personal.empty_title')"
            :empty-description="__('app/stacks/personal.empty_description')"
        />
    </div>
</div>
