<?php

namespace App\Livewire\Forms\Settings\Images;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Gallery : keep the original aspect ratio, convert to webp, store publicly.
 */
trait GalleryUpload
{
    protected function storeGalleryImage(UploadedFile $file): string
    {
        $path = Image::fromUpload($file)
            ->orient()
            ->toWebp()
            ->storePublicly('gallery', config('filesystems.public_disk'));

        throw_if($path === false, RuntimeException::class, 'Failed to store the uploaded gallery image.');

        return Storage::disk(config('filesystems.public_disk'))->url($path);
    }

    protected function deleteGalleryFile(string $url): void
    {
        $disk = Storage::disk(config('filesystems.public_disk'));

        $disk->delete(Str::after($url, $disk->url('')));
    }
}
