<?php

namespace Database\Seeders\Concerns;

use Spatie\MediaLibrary\HasMedia;

/**
 * Attaches images from database/seeders/media/{folder}/ to a model.
 *
 * Media is stored flat in storage/app/public/images/ (FlatPathGenerator), so a
 * file name that already exists there would be overwritten. Pass a $prefix
 * (e.g. "category-") when the seed file names are not already unique.
 */
trait AttachesSeedMedia
{
    protected function mediaPath(string $relative = ''): string
    {
        return database_path('seeders/media' . ($relative !== '' ? '/' . ltrim($relative, '/') : ''));
    }

    /**
     * @param  string|string[]  $files  file names relative to media/{$folder}/, in display order
     */
    protected function attachSeedMedia(HasMedia $model, string $collection, string $folder, string|array $files, string $prefix = ''): int
    {
        if ($model->getMedia($collection)->isNotEmpty()) {
            return 0;
        }

        $attached = 0;
        foreach ((array) $files as $file) {
            $path = $this->mediaPath("{$folder}/{$file}");
            if (! is_file($path)) {
                $this->command?->warn("  Missing seed image: media/{$folder}/{$file}");
                continue;
            }
            try {
                $model->addMedia($path)
                    ->preservingOriginal()
                    ->usingFileName($prefix . basename($file))
                    ->toMediaCollection($collection);
                $attached++;
            } catch (\Throwable $e) {
                $this->command?->warn("  Image failed (media/{$folder}/{$file}): {$e->getMessage()}");
            }
        }

        return $attached;
    }
}
