<?php

namespace App\Livewire\Forms\Settings;

use App\Models\Tool;
use Livewire\Form;

class ToolDangerZoneListingForm extends Form
{
    public ?Tool $tool = null;

    public string $confirmation = '';

    public function setTool(Tool $tool): void
    {
        $this->tool = $tool;
    }

    public function delete(): void
    {
        $this->validate([
            'confirmation' => ['required', 'string', 'in:DELETE'],
        ], [
            'confirmation.in' => 'Please type DELETE to confirm.',
        ]);

        $this->tool->delete();
    }
}
