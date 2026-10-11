<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\AttachesSeedMedia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * Copies database/seeders/media/logo/* to public/images/, where the site expects
 * them (e.g. asset('images/henex-logo.png') in the SEO / JSON-LD markup).
 */
class BrandAssetSeeder extends Seeder
{
    use AttachesSeedMedia;

    /** Source file in media/logo/ => target name in public/images/ */
    private array $files = [
        'henex-logo.png' => 'henex-logo.png',
    ];

    public function run(): void
    {
        File::ensureDirectoryExists(public_path('images'));

        foreach ($this->files as $source => $target) {
            $path = $this->mediaPath("logo/{$source}");
            if (! is_file($path)) {
                $this->command?->warn("  Missing brand asset: media/logo/{$source}");
                continue;
            }
            File::copy($path, public_path("images/{$target}"));
        }
    }
}
