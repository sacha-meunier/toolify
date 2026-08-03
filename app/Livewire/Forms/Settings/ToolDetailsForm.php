<?php

namespace App\Livewire\Forms\Settings;

use App\Enums\ToolHeadcount;
use App\Enums\ToolStatus;
use App\Models\Tool;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ToolDetailsForm extends Form
{
    public ?Tool $tool = null;

    public ?int $foundedYear = null;

    public ?int $firstReleaseYear = null;

    public string $headquarters = '';

    public string $headcount = '';

    public string $status = '';

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'foundedYear' => ['nullable', 'integer', 'min:1970', 'max:'.Carbon::now()->year],
            'firstReleaseYear' => ['nullable', 'integer', 'min:1970', 'max:'.Carbon::now()->year],
            'headquarters' => ['nullable', 'string', 'max:255'],
            'headcount' => ['nullable', Rule::enum(ToolHeadcount::class)],
            'status' => ['required', Rule::enum(ToolStatus::class)],
        ];
    }

    public function setTool(Tool $tool): void
    {
        $this->tool = $tool;
        $this->foundedYear = $tool->founded_year;
        $this->firstReleaseYear = $tool->first_release_year;
        $this->headquarters = $tool->headquarters ?? '';
        $this->headcount = $tool->headcount?->value ?? '';
        $this->status = $tool->status->value;
    }

    public function update(): void
    {
        $this->validate();

        $this->tool->update([
            'founded_year' => $this->foundedYear,
            'first_release_year' => $this->firstReleaseYear,
            'headquarters' => $this->headquarters ?: null,
            'headcount' => $this->headcount ?: null,
            'status' => $this->status,
        ]);
    }
}
