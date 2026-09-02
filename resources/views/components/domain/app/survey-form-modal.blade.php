<x-ui.modal show="$wire.surveyFormOpen" close="closeSurveyForm" class="max-w-xl">
    <x-slot:header>
        <h2 class="text-lg font-semibold text-foreground">{{ $surveyFormId ? __('app/components/survey-form-modal.title_edit') : __('app/components/survey-form-modal.title_new') }}</h2>
    </x-slot:header>

    <form wire:submit="saveSurveyForm" class="flex flex-col gap-4">
        <x-ui.field x-data="{ length: {{ mb_strlen($surveyForm->name) }} }">
            <x-ui.field.label :content="__('app/components/survey-form-modal.field_name')" required/>
            <x-ui.input wire:model="surveyForm.name" x-on:input="length = $event.target.value.length" name="surveyForm.name" :placeholder="__('app/components/survey-form-modal.field_name_placeholder')" required maxlength="255"/>
            <x-ui.field.error x-show="length >= 255" x-cloak>
                {{ __('components/ui/field.max_length_reached', ['max' => 255]) }}
            </x-ui.field.error>
            <x-ui.field.error :content="$errors->first('surveyForm.name')"/>
        </x-ui.field>

        <x-ui.field x-data="{ length: {{ mb_strlen($surveyForm->query) }} }">
            <x-ui.field.label :content="__('app/components/survey-form-modal.field_query')"/>
            <x-ui.input wire:model="surveyForm.query" x-on:input="length = $event.target.value.length" name="surveyForm.query" :placeholder="__('app/components/survey-form-modal.field_query_placeholder')" maxlength="{{ \App\Models\Survey::QUERY_MAX_LENGTH }}"/>
            <x-ui.field.error x-show="length >= {{ \App\Models\Survey::QUERY_MAX_LENGTH }}" x-cloak>
                {{ __('components/ui/field.max_length_reached', ['max' => \App\Models\Survey::QUERY_MAX_LENGTH]) }}
            </x-ui.field.error>
            <x-ui.field.error :content="$errors->first('surveyForm.query')"/>
        </x-ui.field>

        <div class="flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <x-ui.field.label :content="__('app/components/survey-form-modal.field_filters')"/>
                @if (collect($surveyForm->filters)->flatten()->isNotEmpty())
                    <button type="button" wire:click="clearSurveyFormFilters" class="text-sm text-muted-foreground hover:text-foreground">
                        {{ __('app/components/survey-form-modal.clear_all_filters') }}
                    </button>
                @endif
            </div>

            <div class="flex flex-col gap-4 rounded-md border border-border p-3">
                @foreach ([
                    ['group' => 'pricing', 'label' => __('app/components/survey-form-modal.filter_group_price'), 'cases' => \App\Enums\Pricing::cases()],
                    ['group' => 'platforms', 'label' => __('app/components/survey-form-modal.filter_group_platform'), 'cases' => \App\Enums\Platform::cases()],
                    ['group' => 'categories', 'label' => __('app/components/survey-form-modal.filter_group_category'), 'cases' => \App\Enums\Category::cases()],
                ] as $type)
                    <div class="flex flex-col gap-1.5">
                        <p class="text-xs font-medium text-muted-foreground">{{ $type['label'] }}</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($type['cases'] as $case)
                                <button
                                    type="button"
                                    wire:click="toggleSurveyFormFilter('{{ $type['group'] }}', '{{ $case->value }}')"
                                    class="flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-xs transition-[color,background-color,border-color,transform] duration-150 ease-out active:scale-(--scale-press) {{ in_array($case->value, $surveyForm->filters[$type['group']], true) ? 'border-primary bg-primary text-primary-foreground' : 'border-border text-foreground hover:bg-muted' }}"
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
            <x-ui.button type="button" variant="outline" :label="__('app/components/survey-form-modal.cancel')" wire:click="closeSurveyForm"/>
            <x-ui.button type="submit" variant="primary" :label="__('app/components/survey-form-modal.save_survey')"/>
        </div>
    </form>
</x-ui.modal>
