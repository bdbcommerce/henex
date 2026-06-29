<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['slug' => 'desktop-barcode-scanners', 'name' => ['uz' => 'Stol Shtrix-kod Skanerlari', 'ru' => 'Настольные сканеры штрих-кода', 'en' => 'Desktop Barcode Scanners']],
            ['slug' => 'wired-barcode-scanners', 'name' => ['uz' => 'Simsiz Shtrix-kod Skanerlari', 'ru' => 'Проводные сканеры штрих-кода', 'en' => 'Wired Barcode Scanners']],
            ['slug' => 'wireless-barcode-scanners', 'name' => ['uz' => 'Sim-sizlik Shtrix-kod Skanerlari', 'ru' => 'Беспроводные сканеры штрих-кода', 'en' => 'Wireless Barcode Scanners']],
            ['slug' => 'wearable-barcode-scanners', 'name' => ['uz' => 'Kiyim Shtrix-kod Skanerlari', 'ru' => 'Носимые сканеры штрих-кода', 'en' => 'Wearable Barcode Scanners']],
            ['slug' => 'industrial-barcode-scanners', 'name' => ['uz' => 'Sanoat Shtrix-kod Skanerlari', 'ru' => 'Промышленные сканеры штрих-кода', 'en' => 'Industrial Barcode Scanners']],
            ['slug' => 'barcode-scanner-modules', 'name' => ['uz' => 'Shtrix-kod Skaneri Modullar', 'ru' => 'Модули сканеров штрих-кода', 'en' => 'Barcode Scanner Modules']],
            ['slug' => 'data-collectors', 'name' => ['uz' => 'Ma\'lumot Yig\'uvchilar', 'ru' => 'Сборщики данных', 'en' => 'Data Collectors']],
            ['slug' => 'pos-terminals', 'name' => ['uz' => 'POS Terminallar', 'ru' => 'POS терминалы', 'en' => 'POS Terminals']],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
