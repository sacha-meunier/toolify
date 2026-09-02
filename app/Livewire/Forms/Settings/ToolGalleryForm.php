<?php

namespace App\Livewire\Forms\Settings;

use App\Livewire\Forms\Settings\Images\BannerUpload;
use App\Livewire\Forms\Settings\Images\GalleryUpload;
use App\Models\Tool;
use Livewire\Form;

class ToolGalleryForm extends Form
{
    use BannerUpload;
    use GalleryUpload;

    public const int MAX_IMAGES = 8;

    public const int MAX_FILE_SIZE_KB = 4096;

    public ?Tool $tool = null;

    public mixed $banner = null;

    /** @var array<int, mixed> */
    public array $newImages = [];

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'banner' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:'.self::MAX_FILE_SIZE_KB],
            'newImages' => ['array'],
            'newImages.*' => ['image', 'mimes:jpeg,png,webp', 'max:'.self::MAX_FILE_SIZE_KB],
        ];
    }

    public function setTool(Tool $tool): void
    {
        $this->tool = $tool;
    }

    public function update(): void
    {
        $this->validate();

        $existing = $this->tool->gallery?->all() ?? [];

        if (count($existing) + count($this->newImages) > self::MAX_IMAGES) {
            $this->addError('newImages', trans('app/settings/teams/listing/gallery.max_images_error', ['max' => self::MAX_IMAGES]));

            return;
        }

        $attributes = ['gallery' => $existing];

        if ($this->banner) {
            $attributes['banner_url'] = $this->storeBanner($this->banner, $this->tool->banner_url);
        }

        foreach ($this->newImages as $image) {
            $attributes['gallery'][] = $this->storeGalleryImage($image);
        }

        $this->tool->update($attributes);

        $this->reset('banner', 'newImages');
    }

    public function removeImage(int $index): void
    {
        $images = $this->tool->gallery?->all() ?? [];

        if (! array_key_exists($index, $images)) {
            return;
        }

        $this->deleteGalleryFile($images[$index]);

        unset($images[$index]);

        $this->tool->update(['gallery' => array_values($images)]);
    }

    public function removeBanner(): void
    {
        if (! $this->tool || ! $this->tool->banner_url) {
            return;
        }

        $this->deleteBannerFile($this->tool->banner_url);

        $this->tool->update(['banner_url' => null]);
    }
}
