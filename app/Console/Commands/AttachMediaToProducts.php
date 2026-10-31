<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class AttachMediaToProducts extends Command
{
    protected $signature = 'attach:media';
    protected $description = 'Attach cached local images from .claude/store to products in database';

    public function handle()
    {
        $storeDir = base_path('.claude/store');
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->error('No products found in database. Import products first.');
            return 1;
        }

        $this->info("Attaching cached images to {$products->count()} products...\n");

        $totalImages = 0;
        $attached = 0;
        $failed = 0;

        foreach ($products as $product) {
            $productPath = "$storeDir/{$product->name}";

            if (!is_dir($productPath)) {
                $this->warn("  ⚠️  No folder for {$product->name}");
                continue;
            }

            $images = array_filter(glob("$productPath/*.jpg"), fn($f) => is_file($f));

            if (empty($images)) {
                continue;
            }

            foreach ($images as $imagePath) {
                $totalImages++;
                $filename = basename($imagePath);

                try {
                    $product->addMedia($imagePath)
                        ->usingFileName($filename)
                        ->toMediaCollection('gallery');

                    $attached++;
                    $this->line("  ✅ {$product->name}/$filename");
                } catch (\Exception $e) {
                    $failed++;
                    $this->warn("  ❌ Failed: {$product->name}/$filename — {$e->getMessage()}");
                }
            }
        }

        $this->info("\n✅ Image Attachment Complete!");
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->line("Total images:  $totalImages");
        $this->line("Attached:      $attached ✅");
        $this->line("Failed:        $failed ❌");
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        // Verify
        $productsWithMedia = Product::has('media')->count();
        $totalMedia = \Spatie\MediaLibrary\Models\Media::count();

        $this->info("\nVerification:");
        $this->line("  Products with images: $productsWithMedia / " . Product::count());
        $this->line("  Total media files:    $totalMedia");

        return 0;
    }
}
