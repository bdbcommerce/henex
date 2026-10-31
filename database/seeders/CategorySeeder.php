<?php

namespace Database\Seeders;

use App\Models\Category;
use Database\Seeders\Concerns\AttachesSeedMedia;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use AttachesSeedMedia;

    /**
     * Run the database seeds.
     *
     * 'image' is a file name inside database/seeders/media/categories/ (optional — a
     * missing file only prints a warning).
     */
    public function run(): void
    {
        $categories = [
            ['slug' => 'desktop-barcode-scanners', 'image' => 'desktop-barcode-scanners.jpg', 'name' => ['uz' => 'Stol Shtrix-kod Skanerlari', 'ru' => 'Настольные сканеры штрих-кода', 'en' => 'Desktop Barcode Scanners']],
            ['slug' => 'wired-barcode-scanners', 'image' => 'wired-barcode-scanners.jpg', 'name' => ['uz' => 'Simsiz Shtrix-kod Skanerlari', 'ru' => 'Проводные сканеры штрих-кода', 'en' => 'Wired Barcode Scanners']],
            ['slug' => 'wireless-barcode-scanners', 'image' => 'wireless-barcode-scanners.jpg', 'name' => ['uz' => 'Sim-sizlik Shtrix-kod Skanerlari', 'ru' => 'Беспроводные сканеры штрих-кода', 'en' => 'Wireless Barcode Scanners']],
            ['slug' => 'wearable-barcode-scanners', 'image' => 'wearable-barcode-scanners.jpg', 'name' => ['uz' => 'Kiyim Shtrix-kod Skanerlari', 'ru' => 'Носимые сканеры штрих-кода', 'en' => 'Wearable Barcode Scanners']],
            ['slug' => 'industrial-barcode-scanners', 'image' => 'industrial-barcode-scanners.jpg', 'name' => ['uz' => 'Sanoat Shtrix-kod Skanerlari', 'ru' => 'Промышленные сканеры штрих-кода', 'en' => 'Industrial Barcode Scanners']],
            ['slug' => 'barcode-scanner-modules', 'image' => 'barcode-scanner-modules.jpg', 'name' => ['uz' => 'Shtrix-kod Skaneri Modullar', 'ru' => 'Модули сканеров штрих-кода', 'en' => 'Barcode Scanner Modules']],
            ['slug' => 'data-collectors', 'image' => 'data-collectors.jpg', 'name' => ['uz' => 'Ma\'lumot Yig\'uvchilar', 'ru' => 'Сборщики данных', 'en' => 'Data Collectors']],
            ['slug' => 'pos-terminals', 'image' => 'pos-terminals.jpg', 'name' => ['uz' => 'POS Terminallar', 'ru' => 'POS терминалы', 'en' => 'POS Terminals']],
        ];

        foreach ($categories as $data) {
            $image = $data['image'] ?? null;
            unset($data['image']);

            $category = Category::create($data);

            if ($image) {
                $this->attachSeedMedia($category, 'image', 'categories', $image, prefix: 'category-');
            }
        }
    }
}
