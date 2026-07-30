<x-ui.modal show="$wire.surveyFormOpen" close="closeSurveyForm" class="max-w-xl">
    <x-slot:header>
        <h2 class="text-lg font-semibold text-foreground">{{ $surveyFormId ? 'Edit survey' : 'New survey' }}</h2>
    </x-slot:header>

    <form wire:submit="saveSurveyForm" class="flex flex-col gap-4">
        <x-ui.field>
            <x-ui.field.label content="Name"/>
            <x-ui.input wire:model="surveyForm.name" name="surveyForm.name" placeholder="e.g. Free project management tools"/>
            <x-ui.field.error :content="$errors->first('surveyForm.name')"/>
        </x-ui.field>

        <x-ui.field>
            <x-ui.field.label content="Search query"/>
            <x-ui.input wire:model="surveyForm.query" name="surveyForm.query" placeholder="Keywords to search for (optional)"/>
            <x-ui.field.error :content="$errors->first('surveyForm.query')"/>
        </x-ui.field>

        <div class="flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <x-ui.field.label content="Filters"/>
                @if (collect($surveyForm->filters)->flatten()->isNotEmpty())
                    <button type="button" wire:click="clearSurveyFormFilters" class="text-sm text-muted-foreground hover:text-foreground">
                        Clear all filters
                    </button>
                @endif
            </div>

            <div class="flex flex-col gap-4 rounded-md border border-border p-3">
                @foreach ([
                    ['group' => 'pricing', 'label' => 'Price', 'cases' => \App\Enums\Pricing::cases()],
                    ['group' => 'platforms', 'label' => 'Platform', 'cases' => \App\Enums\Platform::cases()],
                    ['group' => 'categories', 'label' => 'Category', 'cases' => \App\Enums\Category::cases()],
                ] as $type)
                    <div class="flex flex-col gap-1.5">
                        <p class="text-xs font-medium text-muted-foreground">{{ $type['label'] }}</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($type['cases'] as $case)
                                <button
                                    type="button"
                                    wire:click="toggleSurveyFormFilter('{{ $type['group'] }}', '{{ $case->value }}')"
                                    class="flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs {{ in_array($case->value, $surveyForm->filters[$type['group']], true) ? 'border-primary bg-primary text-primary-foreground' : 'border-border text-foreground hover:bg-muted' }}"
                                >
                                    {{ $case->label() }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2">
            <x-ui.button type="button" variant="outline" label="Cancel" wire:click="closeSurveyForm"/>
            <x-ui.button type="submit" variant="primary" label="Save survey"/>
        </div>
    </form>
</x-ui.modal>
