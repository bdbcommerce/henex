<?php

namespace App\Support\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

/**
 * Stores every media file — originals, conversions, and responsive images —
 * flat under a single "images/" directory instead of Spatie's default
 * per-media-id folder. Relies on file_name staying unique across all media
 * (already guaranteed by our upload naming: "{slug}-{n}.jpg" from the seeder,
 * "{time()}_{originalName}" from admin uploads); conversion/responsive
 * variants can't collide with their own original since MediaLibrary suffixes
 * them ("-thumb", "-medium", etc.) before the extension.
 */
class FlatPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return 'images/';
    }

    public function getPathForConversions(Media $media): string
    {
        return 'images/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return 'images/';
    }
}
