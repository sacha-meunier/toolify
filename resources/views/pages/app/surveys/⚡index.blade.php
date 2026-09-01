<?php

use App\Livewire\Traits\ManagesSurveyForm;
use App\Livewire\Traits\ManagesSurveys;
use App\Models\Survey;
use App\Models\Team;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    use ManagesSurveyForm, ManagesSurveys;

    public string $scope;

    public ?Team $team = null;

    public function mount(string $scope, ?Team $team = null): void
    {
        $this->scope = $scope;
        $this->team = $team;

        if ($scope === 'team') {
            Gate::authorize('manageSurveys', $this->team);
        }
    }

    #[Computed]
    public function workspace(): ?Workspace
    {
        return auth()->user()->currentWorkspace();
    }

    #[Computed]
    public function scopeLabel(): string
    {
        return match ($this->scope) {
            'workspace' => __('app/surveys.scope_workspace'),
            'team' => $this->team->name,
            default => __('app/surveys.scope_personal'),
        };
    }

    /**
     * The owner whose surveys this page lists. Null only for the workspace scope when the
     * user isn't part of one yet, in which case the view shows a dedicated empty state.
     */
    #[Computed]
    public function scopeOwner(): User|Workspace|Team|null
    {
        return match ($this->scope) {
            'workspace' => $this->workspace,
            'team' => $this->team,
            default => auth()->user(),
        };
    }

    #[Computed]
    public function surveys(): Collection
    {
        return $this->scopeOwner?->surveys()->latest()->get() ?? new Collection;
    }

    protected function surveyFormOwner(): Model
    {
        return match ($this->scope) {
            'workspace' => $this->workspace ?? abort(404),
            'team' => $this->team ?? abort(404),
            default => auth()->user(),
        };
    }

    protected function afterSurveyFormSaved(Survey $survey): void
    {
        unset($this->surveys);
    }
};
?>

<div class="flex flex-1 flex-col">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="[__('app/surveys.breadcrumb_root') => null, $this->scopeLabel => null]"/>

        @if ($this->scope !== 'workspace' || $this->workspace)
            <x-slot:actions>
                <x-ui.button
                    variant="primary"
                    icon="add-01"
                    :label="__('app/surveys.cta_new_survey')"
                    wire:click="openSurveyForm"
                />
            </x-slot:actions>
        @endif
    </x-domain.app.topbar>

    <div class="flex flex-1 flex-col gap-6 px-4 py-6 lg:px-8 lg:py-8">
        @if ($this->scope === 'workspace' && ! $this->workspace)
            <div class="flex flex-1 items-center justify-center">
                <x-domain.app.empty-state
                    icon="tool-view"
                    :title="__('app/surveys.no_workspace_title')"
                    :description="__('app/surveys.no_workspace_description')"
                />
            </div>
        @else
            <x-domain.app.survey-list
                :surveys="$this->surveys"
                :empty-title="__('app/surveys.empty_title')"
                :empty-description="__('app/surveys.empty_description')"
            >
                <x-ui.button
                    variant="primary"
                    icon="add-01"
                    :label="__('app/surveys.cta_create_survey')"
                    wire:click="openSurveyForm"
                    class="mt-2"
                />
            </x-domain.app.survey-list>
        @endif
    </div>

    @include('components.domain.app.survey-form-modal')
</div>
