<?php

namespace App\Livewire\Forms;

use App\Models\Survey;
use Livewire\Form;

class SurveyForm extends Form
{
    public string $name = '';

    public string $query = '';

    /**
     * @var array{pricing: array<string>, categories: array<string>, platforms: array<string>}
     */
    public array $filters = [
        'pricing' => [],
        'categories' => [],
        'platforms' => [],
    ];

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'query' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Populate the form from an existing survey, for editing.
     */
    public function fillFromSurvey(Survey $survey): void
    {
        $this->name = $survey->name;
        $this->query = $survey->query ?? '';
        $this->filters = $survey->filters;
    }

    /**
     * Reset the form to a blank state, optionally pre-filling the name/query from a search query.
     */
    public function resetForQuery(?string $query = null): void
    {
        $this->reset();

        $this->name = $query ?? '';
        $this->query = $query ?? '';
    }

    public function toggleFilter(string $group, string $value): void
    {
        $this->filters[$group] = in_array($value, $this->filters[$group], true)
            ? array_values(array_diff($this->filters[$group], [$value]))
            : [...$this->filters[$group], $value];
    }

    public function clearFilters(): void
    {
        $this->filters = [
            'pricing' => [],
            'categories' => [],
            'platforms' => [],
        ];
    }

    /**
     * @return array{name: string, query: string|null, filters: array<string, array<int, string>>}
     */
    public function attributes(): array
    {
        $validated = $this->validate();

        return [
            ...$validated,
            'query' => $validated['query'] !== '' ? $validated['query'] : null,
            'filters' => $this->filters,
        ];
    }
}
