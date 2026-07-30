<?php

namespace App\Livewire\Traits;

use App\Livewire\Forms\SurveyForm;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

/**
 * The survey create/edit form.
 */
trait ManagesSurveyForm
{
    public bool $surveyFormOpen = false;

    public ?int $surveyFormId = null;

    public SurveyForm $surveyForm;

    public function openSurveyForm(?int $surveyId = null, ?string $query = null): void
    {
        $this->resetValidation();
        $this->surveyFormId = $surveyId;

        if ($surveyId) {
            $survey = Survey::findOrFail($surveyId);
            Gate::authorize('update', $survey);

            $this->surveyForm->fillFromSurvey($survey);
        } else {
            $this->surveyForm->resetForQuery($query);
        }

        $this->surveyFormOpen = true;
    }

    public function toggleSurveyFormFilter(string $group, string $value): void
    {
        $this->surveyForm->toggleFilter($group, $value);
    }

    public function clearSurveyFormFilters(): void
    {
        $this->surveyForm->clearFilters();
    }

    public function saveSurveyForm(): void
    {
        $attributes = $this->surveyForm->attributes();

        if ($this->surveyFormId) {
            $survey = Survey::findOrFail($this->surveyFormId);
            Gate::authorize('update', $survey);
            $survey->fill($attributes)->save();
        } else {
            $owner = $this->surveyFormOwner();

            if (! $owner instanceof User) {
                Gate::authorize('manageSurveys', $owner);
            }

            $survey = $owner->surveys()->create($attributes);
        }

        $this->surveyFormOpen = false;

        $this->afterSurveyFormSaved($survey);
    }

    public function closeSurveyForm(): void
    {
        $this->surveyFormOpen = false;
    }

    /**
     * The owner (User/Workspace/Team) a new survey should be created for. Only called when
     * creating a survey, so pages that only ever edit an existing one can just throw here.
     */
    abstract protected function surveyFormOwner(): Model;

    /**
     * Called after a survey is created or updated, so the host page can refresh whatever
     * it has cached (a survey list, the currently displayed survey, etc).
     */
    abstract protected function afterSurveyFormSaved(Survey $survey): void;
}
