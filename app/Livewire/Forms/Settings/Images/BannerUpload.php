<?php

namespace App\Livewire\Forms\Settings\Images;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Banner : keep the original aspect ratio, convert to webp, store publicly.
 */
trait BannerUpload
{
    protected function storeBanner(UploadedFile $file, ?string $existingUrl): string
    {
        if ($existingUrl) {
            $this->deleteBannerFile($existingUrl);
        }

        $path = Image::fromUpload($file)
            ->orient()
            ->toWebp()
            ->storePublicly('banners', config('filesystems.public_disk'));

        throw_if($path === false, RuntimeException::class, 'Failed to store the uploaded banner.');

        return Storage::disk(config('filesystems.public_disk'))->url($path);
    }

    protected function deleteBannerFile(string $url): void
    {
        $disk = Storage::disk(config('filesystems.public_disk'));

        $disk->delete(Str::after($url, $disk->url('')));
    }
}
