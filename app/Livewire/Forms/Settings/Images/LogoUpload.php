<?php

namespace App\Livewire\Forms\Settings\Images;

use Illuminate\Http\UploadedFile;
use Illuminate\Image\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Logo-specific: crop to a square, convert to webp, store publicly.
 */
trait LogoUpload
{
    protected function storeLogo(UploadedFile $file, ?string $existingUrl): string
    {
        if ($existingUrl) {
            $this->deleteLogoFile($existingUrl);
        }

        $path = (new Image(fn () => $file->getContent(), $file))
            ->cover(128, 128)
            ->toWebp()
            ->store('logos', config('filesystems.public_disk'));

        throw_if($path === false, RuntimeException::class, 'Failed to store the uploaded logo.');

        return Storage::disk(config('filesystems.public_disk'))->url($path);
    }

    protected function deleteLogoFile(string $url): void
    {
        $disk = Storage::disk(config('filesystems.public_disk'));

        $disk->delete(Str::after($url, $disk->url('')));
    }
}
