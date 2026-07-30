<?php

namespace App\Livewire\Traits;

use App\Models\Survey;
use Illuminate\Support\Facades\Gate;

/**
 * Shared duplicate/delete actions for the personal, workspace, and team survey index pages.
 */
trait ManagesSurveys
{
    public function duplicate(int $id): void
    {
        $survey = Survey::with('owner')->findOrFail($id);
        Gate::authorize('view', $survey);

        $survey->owner->surveys()->create([
            'name' => "{$survey->name} (copy)",
            'query' => $survey->query,
            'filters' => $survey->filters,
        ]);

        unset($this->surveys);
    }

    public function delete(int $id): void
    {
        $survey = Survey::findOrFail($id);
        Gate::authorize('delete', $survey);
        $survey->delete();

        unset($this->surveys);
    }
}
