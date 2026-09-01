<?php

use App\Livewire\Traits\ManagesSurveyForm;
use App\Models\Survey;
use App\Models\Team;
use App\Models\Tool;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    use ManagesSurveyForm;

    public Survey $survey;

    public function mount(Survey $survey): void
    {
        Gate::authorize('view', $survey);

        $this->survey = $survey;
        $survey->markVisited();
    }

    #[Computed]
    public function tools(): Collection
    {
        return Tool::matching($this->survey->query ?? '', $this->survey->filters)->orderBy('name')->get();
    }

    #[Computed]
    public function scopeLabel(): string
    {
        return match (true) {
            $this->survey->owner instanceof Workspace => __('app/surveys/show.scope_workspace'),
            $this->survey->owner instanceof Team => $this->survey->owner->name,
            default => __('app/surveys/show.scope_personal'),
        };
    }

    #[Computed]
    public function scopeUrl(): string
    {
        return match (true) {
            $this->survey->owner instanceof Workspace => route('surveys.workspace'),
            $this->survey->owner instanceof Team => route('surveys.teams.show', $this->survey->owner),
            default => route('surveys.personal'),
        };
    }

    protected function surveyFormOwner(): Model
    {
        // This page only ever edits an existing survey (whose scope is fixed at creation),
        // never creates one, so there's no owner to resolve here.
        throw new \LogicException('Surveys cannot be created from the show page.');
    }

    protected function afterSurveyFormSaved(Survey $survey): void
    {
        $this->survey->refresh();
        unset($this->tools, $this->scopeLabel, $this->scopeUrl);
    }
};
?>

<div class="flex flex-1 flex-col">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="[__('app/surveys/show.breadcrumb_root') => route('surveys.personal'), $this->scopeLabel => $this->scopeUrl, $survey->name => null]"/>

        <x-slot:actions>
            <x-ui.button
                variant="outline"
                icon="pen-01"
                :label="__('app/surveys/show.edit')"
                wire:click="openSurveyForm({{ $survey->id }})"
            />
        </x-slot:actions>
    </x-domain.app.topbar>

    <div class="flex flex-1 flex-col gap-6 px-4 py-6 lg:px-8 lg:py-8">
        @if ($this->tools->isNotEmpty())
            <div class="flex flex-col divide-y border border-border rounded-md">
                @foreach ($this->tools as $tool)
                    <x-domain.app.tool-list-item :tool="$tool"/>
                @endforeach
            </div>
        @else
            <div class="flex flex-1 items-center justify-center">
                <x-domain.app.empty-state
                    icon="search-01"
                    :title="__('app/surveys/show.empty_title')"
                    :description="__('app/surveys/show.empty_description')"
                >
                    <div class="flex items-center gap-2 mt-2">
                        <x-ui.button
                            variant="secondary"
                            icon="pen-01"
                            :label="__('app/surveys/show.cta_edit_survey')"
                            wire:click="openSurveyForm({{ $survey->id }})"
                        />
                        <x-ui.button variant="outline" :label="__('app/surveys/show.cta_notification_settings')" :href="route('settings.account.notifications')" wire:navigate/>
                    </div>
                </x-domain.app.empty-state>
            </div>
        @endif
    </div>

    @include('components.domain.app.survey-form-modal')
</div>
