<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportHenexProducts extends Command
{
    protected $signature = 'import:henex-products {--skip-images : Skip image downloads}';
    protected $description = 'Import 67 HENEX products from .claude/store metadata with images';

    public function handle()
    {
        $storeDir = base_path('.claude/store');

        if (!is_dir($storeDir)) {
            $this->error("Store directory not found: $storeDir");
            return 1;
        }

        $this->info("Loading product metadata from $storeDir");

        $productDirs = array_diff(scandir($storeDir), ['.', '..', 'index.json']);
        $imported = 0;
        $skipped = 0;

        foreach ($productDirs as $productName) {
            $productPath = "$storeDir/$productName";
            $metadataFile = "$productPath/metadata.json";

            if (!file_exists($metadataFile)) {
                continue;
            }

            $metadata = json_decode(file_get_contents($metadataFile), true);

            // Check if product already exists
            if (Product::where('sku', $metadata['id'])->exists()) {
                $this->line("⏭️  Skipped {$metadata['name']} (already exists)");
                $skipped++;
                continue;
            }

            try {
                // Create product
                $product = Product::create([
                    'sku' => (string)$metadata['id'],
                    'slug' => Str::slug($metadata['name']),
                    'name' => ['uz' => $metadata['name'], 'ru' => $metadata['name'], 'en' => $metadata['name']],
                    'short_description' => [
                        'uz' => $metadata['subtitle'],
                        'ru' => $metadata['subtitle'],
                        'en' => $metadata['subtitle'],
                    ],
                    'description' => [
                        'uz' => $this->formatDescription($metadata),
                        'ru' => $this->formatDescription($metadata),
                        'en' => $this->formatDescription($metadata),
                    ],
                    'is_active' => true,
                    'is_featured' => false,
                ]);

                // Find and attach category
                $category = Category::where('slug', Str::slug($metadata['category']))->first();
                if ($category) {
                    $product->categories()->attach($category);
                }

                // Download and attach images
                if (!$this->option('skip-images')) {
                    $this->downloadProductImages($product, $metadata, $productPath);
                }

                $this->line("✅ Imported {$metadata['name']} ({$metadata['id']})");
                $imported++;
            } catch (\Exception $e) {
                $this->warn("❌ Failed to import {$metadata['name']}: {$e->getMessage()}");
                $skipped++;
            }
        }

        $this->info("\n✅ Import complete!");
        $this->info("Imported: $imported products");
        $this->info("Skipped: $skipped products");

        return 0;
    }

    private function downloadProductImages(Product $product, array $metadata, string $productPath): void
    {
        if (empty($metadata['images'])) {
            return;
        }

        foreach ($metadata['images'] as $image) {
            try {
                $this->downloadImage($product, $image, $productPath);
            } catch (\Exception $e) {
                $this->warn("   ⚠️  Image download failed: {$image['url']}");
            }
        }
    }

    private function downloadImage(Product $product, array $image, string $productPath): void
    {
        $url = $image['url'];
        $filename = $image['filename'];
        $localPath = "$productPath/$filename";

        // Try local cache first
        if (file_exists($localPath)) {
            $product->addMedia($localPath)
                ->usingFileName($filename)
                ->toMediaCollection('gallery');
            return;
        }

        // Download from URL
        try {
            $imageData = file_get_contents($url, false, stream_context_create([
                'http' => ['timeout' => 10],
                'https' => ['timeout' => 10],
            ]));

            if ($imageData === false) {
                throw new \Exception("Failed to fetch: $url");
            }

            // Save locally
            file_put_contents($localPath, $imageData);

            // Add to product
            $product->addMedia($localPath)
                ->usingFileName($filename)
                ->toMediaCollection('gallery');
        } catch (\Exception $e) {
            throw new \Exception("Image download error: {$e->getMessage()}");
        }
    }

    private function formatDescription(array $metadata): string
    {
        $html = "<h3>{$metadata['subtitle']}</h3>";

        if (!empty($metadata['features'])) {
            $html .= "<h4>Features</h4><p>{$metadata['features']}</p>";
        }

        if (!empty($metadata['application'])) {
            $html .= "<h4>Applications</h4><p>{$metadata['application']}</p>";
        }

        return $html;
    }
}
