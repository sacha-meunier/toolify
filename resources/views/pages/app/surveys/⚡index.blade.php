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
            'workspace' => 'Workspace',
            'team' => $this->team->name,
            default => 'Personal',
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

<div class="flex flex-col">
    <x-domain.app.topbar>
        <x-domain.app.topbar.breadcrumb :items="['Surveys' => null, $this->scopeLabel => null]"/>

        @if ($this->scope !== 'workspace' || $this->workspace)
            <x-slot:actions>
                <x-ui.button
                    variant="primary"
                    icon="add-01"
                    label="New survey"
                    wire:click="openSurveyForm"
                />
            </x-slot:actions>
        @endif
    </x-domain.app.topbar>

    <div class="flex flex-col gap-6 px-8 py-8">
        @if ($this->scope === 'workspace' && ! $this->workspace)
            <x-domain.app.empty-state
                icon="tool-view"
                title="You're not part of a workspace yet"
                description="Join or create a workspace to start saving surveys to it."
            />
        @else
            <x-domain.app.survey-list
                :surveys="$this->surveys"
                empty-title="No surveys yet"
                empty-description="Save a search to get notified when new tools match it."
            >
                <x-ui.button
                    variant="primary"
                    icon="add-01"
                    label="Create a survey"
                    wire:click="openSurveyForm"
                    class="mt-2"
                />
            </x-domain.app.survey-list>
        @endif
    </div>

    @include('components.domain.app.survey-form-modal')
</div>
