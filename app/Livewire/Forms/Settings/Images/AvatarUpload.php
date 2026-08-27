<?php

namespace App\Livewire\Forms\Settings\Images;

use Illuminate\Http\UploadedFile;
use Illuminate\Image\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Avatar-specific: crop to a square, convert to webp, store publicly.
 */
trait AvatarUpload
{
    protected function storeAvatar(UploadedFile $file, ?string $existingUrl): string
    {
        if ($existingUrl) {
            $this->deleteAvatarFile($existingUrl);
        }

        $path = (new Image(fn () => $file->getContent(), $file))
            ->cover(128, 128)
            ->toWebp()
            ->store('avatars', config('filesystems.public_disk'));

        throw_if($path === false, RuntimeException::class, 'Failed to store the uploaded avatar.');

        return Storage::disk(config('filesystems.public_disk'))->url($path);
    }

    protected function deleteAvatarFile(string $url): void
    {
        $disk = Storage::disk(config('filesystems.public_disk'));

        $disk->delete(Str::after($url, $disk->url('')));
    }
}
