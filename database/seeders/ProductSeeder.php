<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /** Maps the scraped henex.cn category to one or more of our seeded category slugs. */
    private array $categoryMap = [
        'Wireless Barcode Scanner' => ['wireless-barcode-scanners'],
        'Wired Barcode Scanner' => ['wired-barcode-scanners'],
        'Desktop Barcode Scanner' => ['desktop-barcode-scanners'],
        'Barcode Scanner Module' => ['barcode-scanner-modules'],
        'Mobile Data Collector' => ['data-collectors'],
    ];

    /** One flagship model per category, shown in the homepage FeaturedProducts section. */
    private array $featuredSkus = ['H500', 'H310RC', 'H3308SRC', 'H900R', 'H8000', 'H60', 'H4', 'HC-666'];

    public function run(): void
    {
        $items = json_decode(file_get_contents(database_path('seeders/data/henex-products.json')), true);
        $categories = Category::all()->keyBy('slug');
        $imageFiles = collect(glob(database_path('seeders/data/images/*.jpg')));

        foreach ($items as $index => $item) {
            $name = $item['name'];
            $slug = Str::slug($name);
            $subtitle = $item['subtitle'];
            $application = $item['application'];
            $features = array_values(array_filter(array_map('trim', explode('|', $item['features']))));

            $description = '<p>' . e($subtitle) . '. Recommended for: ' . e($application) . '.</p><ul>'
                . collect($features)->map(fn ($f) => '<li>' . e($f) . '</li>')->implode('')
                . '</ul>';
            $metaTitle = "{$name} — {$subtitle} | Henex Uzbekistan";
            $metaDescription = "{$name} {$subtitle} from HENEX. {$application}. Official warranty and support in Uzbekistan.";

            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'sku' => $name,
                    'is_featured' => in_array($name, $this->featuredSkus, true),
                    'is_active' => true,
                    'sort_order' => $index,
                    'name' => ['uz' => $name, 'ru' => $name, 'en' => $name],
                    'short_description' => ['uz' => $subtitle, 'ru' => $subtitle, 'en' => $subtitle],
                    'description' => ['uz' => $description, 'ru' => $description, 'en' => $description],
                    'meta_title' => ['uz' => $metaTitle, 'ru' => $metaTitle, 'en' => $metaTitle],
                    'meta_description' => ['uz' => $metaDescription, 'ru' => $metaDescription, 'en' => $metaDescription],
                ]
            );

            $categorySlugs = $this->categoryMap[$item['category']] ?? [];
            $haystack = $subtitle . ' ' . ($item['subcategory'] ?? '');
            if (Str::contains($haystack, 'Industrial')) {
                $categorySlugs[] = 'industrial-barcode-scanners';
            }
            if (Str::contains($haystack, 'Wearable')) {
                $categorySlugs[] = 'wearable-barcode-scanners';
            }
            $categoryIds = collect($categorySlugs)
                ->map(fn ($s) => $categories[$s]->id ?? null)
                ->filter()
                ->unique()
                ->values();
            $product->categories()->sync($categoryIds);

            if ($product->getMedia('gallery')->isEmpty()) {
                // A plain "{$slug}-*.jpg" prefix match would also sweep up sibling variants
                // that share this slug as a prefix (e.g. "hc-3206" matching "hc-3206-ccd-*.jpg"),
                // so require the suffix after the slug to be purely numeric.
                $pattern = '/^' . preg_quote($slug, '/') . '-\d+\.jpg$/';
                $imagePaths = $imageFiles
                    ->filter(fn ($path) => preg_match($pattern, basename($path)))
                    ->sort(fn ($a, $b) => strnatcmp($a, $b))
                    ->values()
                    ->all();
                foreach ($imagePaths as $path) {
                    try {
                        $product->addMedia($path)
                            ->preservingOriginal()
                            ->usingFileName(basename($path))
                            ->toMediaCollection('gallery');
                    } catch (\Throwable $e) {
                        $this->command?->warn("  Image failed for {$name}: {$e->getMessage()}");
                    }
                }
                if (empty($imagePaths)) {
                    $this->command?->warn("  No local images found for {$name} ({$slug}-*.jpg)");
                }
            }

            $this->command?->info("Seeded product: {$name}");
        }
    }
}
