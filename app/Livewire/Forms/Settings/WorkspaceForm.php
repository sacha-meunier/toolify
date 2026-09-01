<?php

namespace App\Livewire\Forms\Settings;

use App\Livewire\Forms\Settings\Images\LogoUpload;
use App\Models\Workspace;
use Livewire\Form;

class WorkspaceForm extends Form
{
    use LogoUpload;

    public ?Workspace $workspace = null;

    public string $name = '';

    public mixed $logo = null;

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:'.Workspace::NAME_MAX_LENGTH],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ];
    }

    public function setWorkspace(Workspace $workspace): void
    {
        $this->workspace = $workspace;
        $this->name = $workspace->name;
    }

    public function update(): void
    {
        $this->validate();

        $this->workspace->update(['name' => $this->name]);

        if ($this->logo) {
            $this->workspace->update([
                'logo_url' => $this->storeLogo($this->logo, $this->workspace->logo_url),
            ]);

            $this->reset('logo');
        }
    }

    public function deleteLogo(): void
    {
        if (! $this->workspace->logo_url) {
            return;
        }

        $this->deleteLogoFile($this->workspace->logo_url);

        $this->workspace->update(['logo_url' => null]);
    }
}
