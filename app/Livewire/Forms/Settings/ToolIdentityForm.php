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

    public const int NAME_MAX_LENGTH = 100;

    public const int TAGLINE_MAX_LENGTH = 255;

    public const int DESCRIPTION_MAX_LENGTH = 500;

    public ?Tool $tool = null;

    public string $name = '';

    /** @var array<string, string> keyed by locale, e.g. ['en' => '...', 'fr' => '...'] */
    public array $tagline = [];

    /** @var array<string, string> keyed by locale */
    public array $description = [];

    /** @var array<int, string> */
    public array $categories = [];

    public mixed $logo = null;

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:'.self::NAME_MAX_LENGTH],
            'tagline' => ['array'],
            'description' => ['array'],
            'categories' => ['required', 'array', 'min:1', 'max:'.Category::MAX_CATEGORIES_PER_TOOL],
            'categories.*' => [Rule::enum(Category::class)],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ];

        // The fallback locale is what every listing page falls back to when a translation
        // is missing, so it's the only one required. The other locales can be filled in later.
        foreach (array_keys(config('app.available_locales')) as $locale) {
            $required = $locale === config('app.fallback_locale');

            $rules["tagline.$locale"] = [$required ? 'required' : 'nullable', 'string', 'max:'.self::TAGLINE_MAX_LENGTH];
            $rules["description.$locale"] = [$required ? 'required' : 'nullable', 'string', 'max:'.self::DESCRIPTION_MAX_LENGTH];
        }

        return $rules;
    }

    public function setTool(Tool $tool): void
    {
        $this->tool = $tool;
        $this->name = $tool->name;
        $this->tagline = $this->withEveryLocale($tool->translations('tagline'));
        $this->description = $this->withEveryLocale($tool->translations('description'));
        $this->categories = $tool->categories->map(fn (Category $category) => $category->value)->all();
    }

    public function update(): void
    {
        $this->validate();

        $this->tool->setTranslations('tagline', $this->tagline);
        $this->tool->setTranslations('description', $this->description);

        $this->tool->update([
            'name' => $this->name,
            'categories' => $this->categories,
        ]);

        if ($this->logo) {
            $this->tool->update([
                'logo_url' => $this->storeLogo($this->logo, $this->tool->logo_url),
            ]);

            $this->reset('logo');
        }
    }

    public function deleteLogo(): void
    {
        if (! $this->tool || ! $this->tool->logo_url) {
            return;
        }

        $this->deleteLogoFile($this->tool->logo_url);

        $this->tool->update(['logo_url' => null]);
    }

    /**
     * Fill in an empty string for every configured locale missing from the tool's stored
     * translations, so the form always has a bound value for each language tab to edit.
     *
     * @param  array<string, string>  $translations
     * @return array<string, string>
     */
    private function withEveryLocale(array $translations): array
    {
        return collect(array_keys(config('app.available_locales')))
            ->mapWithKeys(fn (string $locale) => [$locale => $translations[$locale] ?? ''])
            ->all();
    }
}
