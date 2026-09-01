<?php

namespace App\Livewire\Forms\Settings;

use App\Livewire\Forms\Settings\Images\LogoUpload;
use App\Models\Team;
use App\Models\Workspace;
use Illuminate\Support\Str;
use Livewire\Form;

class TeamCreateForm extends Form
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
            'name' => ['required', 'string', 'max:'.Team::NAME_MAX_LENGTH],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ];
    }

    public function setWorkspace(Workspace $workspace): void
    {
        $this->workspace = $workspace;
    }

    public function store(): Team
    {
        $this->validate();

        $team = $this->workspace->teams()->create([
            'name' => $this->name,
            'slug' => $this->generateUniqueSlug($this->name),
        ]);

        if ($this->logo) {
            $team->update([
                'logo_url' => $this->storeLogo($this->logo, null),
            ]);

            $this->reset('logo');
        }

        return $team;
    }

    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);

        $unique = $slug;

        while (Team::where('slug', $unique)->exists()) {
            $unique = $slug.'-'.random_int(1, 1000000);
        }

        return $unique;
    }
}
