<?php
// Script to attach cached local images to products

require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

$storeDir = base_path('.claude/store');
$products = Product::all();

echo "Attaching cached images to products...\n";

$totalImages = 0;
$attached = 0;
$failed = 0;

foreach ($products as $product) {
    $productPath = "$storeDir/{$product->name}";

    if (!is_dir($productPath)) {
        continue;
    }

    $images = glob("$productPath/*.jpg");

    foreach ($images as $imagePath) {
        $totalImages++;
        $filename = basename($imagePath);

        try {
            $product->addMedia($imagePath)
                ->usingFileName($filename)
                ->toMediaCollection('gallery');

            $attached++;
            echo ".";
        } catch (\Exception $e) {
            $failed++;
            echo "x";
        }
    }
}

echo "\n\n✅ Image Attachment Complete!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Total images:  $totalImages\n";
echo "Attached:      $attached ✅\n";
echo "Failed:        $failed ❌\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Verify
$productsWithMedia = Product::has('media')->count();
$totalMedia = \Spatie\MediaLibrary\Models\Media::count();

echo "\nVerification:\n";
echo "  Products with images: $productsWithMedia / " . Product::count() . "\n";
echo "  Total media files:    $totalMedia\n";
