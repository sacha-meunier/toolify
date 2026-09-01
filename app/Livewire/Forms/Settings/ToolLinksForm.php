<?php

namespace App\Livewire\Forms\Settings;

use App\Models\Tool;
use Livewire\Form;

class ToolLinksForm extends Form
{
    public ?Tool $tool = null;

    public string $websiteUrl = '';

    public string $githubUrl = '';

    public string $twitterUrl = '';

    public string $appStoreUrl = '';

    public string $playStoreUrl = '';

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'websiteUrl' => ['required', 'url', 'max:'.Tool::URL_MAX_LENGTH],
            'githubUrl' => ['nullable', 'url', 'max:'.Tool::URL_MAX_LENGTH],
            'twitterUrl' => ['nullable', 'url', 'max:'.Tool::URL_MAX_LENGTH],
            'appStoreUrl' => ['nullable', 'url', 'max:'.Tool::URL_MAX_LENGTH],
            'playStoreUrl' => ['nullable', 'url', 'max:'.Tool::URL_MAX_LENGTH],
        ];
    }

    public function setTool(Tool $tool): void
    {
        $this->tool = $tool;
        $this->websiteUrl = $tool->website_url;
        $this->githubUrl = $tool->github_url ?? '';
        $this->twitterUrl = $tool->twitter_url ?? '';
        $this->appStoreUrl = $tool->app_store_url ?? '';
        $this->playStoreUrl = $tool->play_store_url ?? '';
    }

    public function update(): void
    {
        $this->validate();

        $this->tool->update([
            'website_url' => $this->websiteUrl,
            'github_url' => $this->githubUrl ?: null,
            'twitter_url' => $this->twitterUrl ?: null,
            'app_store_url' => $this->appStoreUrl ?: null,
            'play_store_url' => $this->playStoreUrl ?: null,
        ]);
    }
}
