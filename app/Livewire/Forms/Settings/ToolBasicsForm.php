<?php

namespace App\Livewire\Forms\Settings;

use App\Enums\ToolVisibility;
use App\Models\Tool;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ToolBasicsForm extends Form
{
    public ?Tool $tool = null;

    public string $visibility = '';

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'visibility' => ['required', Rule::enum(ToolVisibility::class)],
        ];
    }

    public function setTool(Tool $tool): void
    {
        $this->tool = $tool;
        $this->visibility = $tool->visibility->value;
    }

    public function update(): void
    {
        $this->validate();

        $this->tool->update([
            'visibility' => $this->visibility,
        ]);
    }
}
