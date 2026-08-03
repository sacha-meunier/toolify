<?php

namespace App\Livewire\Forms\Settings;

use App\Enums\Category;
use App\Livewire\Forms\Settings\Images\LogoUpload;
use App\Models\Tool;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ToolIdentityForm extends Form
{
    use LogoUpload;

    public ?Tool $tool = null;

    public string $name = '';

    public string $tagline = '';

    public string $description = '';

    /** @var array<int, string> */
    public array $categories = [];

    public mixed $logo = null;

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'categories' => ['required', 'array', 'min:1', 'max:'.Category::MAX_CATEGORIES_PER_TOOL],
            'categories.*' => [Rule::enum(Category::class)],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ];
    }

    public function setTool(Tool $tool): void
    {
        $this->tool = $tool;
        $this->name = $tool->name;
        $this->tagline = $tool->tagline;
        $this->description = $tool->description;
        $this->categories = $tool->categories->map(fn (Category $category) => $category->value)->all();
    }

    public function update(): void
    {
        $this->validate();

        $this->tool->update([
            'name' => $this->name,
            'tagline' => $this->tagline,
            'description' => $this->description,
            'categories' => $this->categories,
        ]);

        if ($this->logo) {
            $this->tool->update([
                'logo_url' => $this->storeLogo($this->logo, $this->tool->logo_url),
            ]);

            $this->reset('logo');
        }
    }
}
