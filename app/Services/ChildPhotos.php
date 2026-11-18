<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ChildPhotos
{
    public const RULES = ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'dimensions:max_width=8000,max_height=8000'];

    public function store(UploadedFile $photo): string
    {
        $path = $photo->store('child-photos', 'local');
        if (! $path) {
            throw new RuntimeException('The child photo could not be saved.');
        }

        return $path;
    }

    public function discard(?string $path): void
    {
        if ($path && preg_match('~^child-photos/[a-zA-Z0-9]+\.(jpg|jpeg|png|webp)$~', $path)) {
            Storage::disk('local')->delete($path);
        }
    }
}
