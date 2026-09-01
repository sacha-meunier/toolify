<?php

namespace App\Livewire\Forms\Settings;

use App\Livewire\Forms\Settings\Images\LogoUpload;
use App\Models\Team;
use Livewire\Form;

class TeamForm extends Form
{
    use LogoUpload;

    public ?Team $team = null;

    public string $name = '';

    public mixed $logo = null;

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:'.Team::NAME_MAX_LENGTH],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ];
    }

    public function setTeam(Team $team): void
    {
        $this->team = $team;
        $this->name = $team->name;
    }

    public function update(): void
    {
        $this->validate();

        $this->team->update(['name' => $this->name]);

        if ($this->logo) {
            $this->team->update([
                'logo_url' => $this->storeLogo($this->logo, $this->team->logo_url),
            ]);

            $this->reset('logo');
        }
    }

    public function deleteLogo(): void
    {
        if (! $this->team->logo_url) {
            return;
        }

        $this->deleteLogoFile($this->team->logo_url);

        $this->team->update(['logo_url' => null]);
    }
}
