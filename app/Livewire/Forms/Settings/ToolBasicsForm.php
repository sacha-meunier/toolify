<?php

namespace App\Livewire\Forms\Settings;

use App\Enums\ToolStatus;
use App\Enums\ToolVisibility;
use App\Models\Tool;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ToolBasicsForm extends Form
{
    public ?Tool $tool = null;

    public string $visibility = '';

    public string $status = '';

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'visibility' => ['required', Rule::enum(ToolVisibility::class)],
            'status' => ['required', Rule::enum(ToolStatus::class)],
        ];
    }

    public function setTool(Tool $tool): void
    {
        $this->tool = $tool;
        $this->visibility = $tool->visibility->value;
        $this->status = $tool->status->value;
    }

    public function update(): void
    {
        $this->validate();

        $this->tool->update([
            'visibility' => $this->visibility,
            'status' => $this->status,
        ]);
    }
}
