<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Small wrapper around Laravel's "public" disk (storage/app/public).
 * Paths are stored in the database relative to that disk, e.g. "projects/abc.jpg".
 */
class Media
{
    /** Public URL for a stored file, or the fallback asset if it is missing. */
    public static function url(?string $path, string $fallback): string
    {
        if ($path && Storage::disk('public')->exists($path)) {
            // asset() follows the host the visitor is using, so it works on
            // localhost, 127.0.0.1 and custom domains without touching APP_URL.
            return asset('storage/'.$path);
        }

        return asset($fallback);
    }

    /**
     * Store a new upload and delete the previous file.
     * With no new upload, the old path is returned unchanged.
     */
    public static function replace(?UploadedFile $file, string $directory, ?string $old = null): ?string
    {
        if (! $file) {
            return $old;
        }

        $new = $file->store($directory, 'public');

        self::delete($old);

        return $new;
    }

    public static function delete(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
