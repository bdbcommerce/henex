<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Database\Seeders\Concerns\AttachesSeedMedia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    use AttachesSeedMedia;

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

    /**
     * Explicit gallery per SKU — file names inside database/seeders/media/products/,
     * in display order (the first one is the main image). Products not listed here
     * fall back to every "{slug}-{n}.jpg" file in that folder.
     */
    private array $images = [
        'H500' => ['h500-1.jpg', 'h500-2.jpg', 'h500-3.jpg'],
        'H310RC' => ['h310rc-1.jpg', 'h310rc-4.jpg', 'h310rc-2.jpg'],
    ];

    public function run(): void
    {
        $items = json_decode(file_get_contents(database_path('seeders/data/henex-products.json')), true);
        $categories = Category::all()->keyBy('slug');
        $imageFiles = collect(glob($this->mediaPath('products/*.jpg')));

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

            $imageNames = $this->images[$name] ?? $this->imagesBySlug($imageFiles, $slug);
            if (empty($imageNames)) {
                $this->command?->warn("  No local images found for {$name} ({$slug}-*.jpg)");
            }
            $this->attachSeedMedia($product, 'gallery', 'products', $imageNames);

            $this->command?->info("Seeded product: {$name}");
        }
    }

    /**
     * A plain "{$slug}-*.jpg" prefix match would also sweep up sibling variants
     * that share this slug as a prefix (e.g. "hc-3206" matching "hc-3206-ccd-*.jpg"),
     * so require the suffix after the slug to be purely numeric.
     */
    private function imagesBySlug(Collection $imageFiles, string $slug): array
    {
        $pattern = '/^' . preg_quote($slug, '/') . '-\d+\.jpg$/';

        return $imageFiles
            ->map(fn ($path) => basename($path))
            ->filter(fn ($file) => preg_match($pattern, $file))
            ->sort(fn ($a, $b) => strnatcmp($a, $b))
            ->values()
            ->all();
    }
}
