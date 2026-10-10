<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Industry;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    public function run(): void
    {
        $industries = [
            ['retail',     '🛒', ['uz' => 'Chakana savdo',          'ru' => 'Розничная торговля',     'en' => 'Retail'],
                ['wired-barcode-scanners', 'wireless-barcode-scanners', 'desktop-barcode-scanners', 'pos-terminals']],
            ['pharmacy',   '💊', ['uz' => 'Dorixona',               'ru' => 'Аптека',                 'en' => 'Pharmacy'],
                ['desktop-barcode-scanners', 'wired-barcode-scanners', 'pos-terminals']],
            ['horeca',     '🍽', ['uz' => 'HoReCa (restoran, kafe)', 'ru' => 'HoReCa (рестораны, кафе)', 'en' => 'HoReCa'],
                ['pos-terminals', 'desktop-barcode-scanners']],
            ['production', '🏭', ['uz' => 'Ishlab chiqarish',       'ru' => 'Производство',           'en' => 'Production'],
                ['industrial-barcode-scanners', 'barcode-scanner-modules', 'data-collectors']],
            ['logistics',  '📦', ['uz' => 'Logistika va ombor',     'ru' => 'Логистика и склад',      'en' => 'Logistics & warehouse'],
                ['wireless-barcode-scanners', 'wearable-barcode-scanners', 'data-collectors', 'industrial-barcode-scanners']],
            ['healthcare', '🏥', ['uz' => 'Tibbiyot',               'ru' => 'Медицина',               'en' => 'Healthcare'],
                ['wireless-barcode-scanners', 'desktop-barcode-scanners']],
            ['other',      '✳️', ['uz' => 'Boshqa',                 'ru' => 'Другое',                 'en' => 'Other'],
                []],
        ];

        foreach ($industries as $i => [$slug, $emoji, $name, $categorySlugs]) {
            $industry = Industry::updateOrCreate(['slug' => $slug], ['name' => $name, 'emoji' => $emoji, 'sort_order' => $i, 'is_active' => true]);
            $industry->categories()->syncWithoutDetaching(Category::whereIn('slug', $categorySlugs)->pluck('id'));
        }
    }
}
