<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Barcode-scanner attributes (uz/ru/en) and their values, then auto-assigns values to
 * products from their name, categories and English description.
 *
 * Safe to re-run: attributes/values are upserted, and a product only receives values
 * for an attribute it has none of yet — manual edits in the admin are never overwritten.
 */
class AttributeSeeder extends Seeder
{
    /** slug => [name, is_multiple, values: key => [uz, ru, en]] */
    private const ATTRIBUTES = [
        'scan-engine' => [
            ['uz' => 'Skanerlash turi', 'ru' => 'Тип сканирования', 'en' => 'Scan engine'], false, [
                '2d'    => ['2D imidjer (QR, DataMatrix)', '2D-имиджер (QR, DataMatrix)', '2D imager (QR, DataMatrix)'],
                'laser' => ['1D lazer', '1D лазерный', '1D laser'],
                'ccd'   => ['1D CCD (chiziqli)', '1D CCD (линейный)', '1D CCD (linear)'],
            ]],
        'connectivity' => [
            ['uz' => 'Ulanish', 'ru' => 'Подключение', 'en' => 'Connectivity'], true, [
                'usb'    => ['USB kabel', 'USB-кабель', 'USB cable'],
                'rs232'  => ['RS-232', 'RS-232', 'RS-232'],
                'bt'     => ['Bluetooth', 'Bluetooth', 'Bluetooth'],
                '24g'    => ['2.4G radio (USB adapter)', 'Радио 2.4G (USB-адаптер)', '2.4G radio (USB dongle)'],
                'cradle' => ['Zaryadlash bazasi (kredl)', 'Зарядная база (кредл)', 'Charging cradle'],
                'wifi4g' => ['Wi-Fi / 4G', 'Wi-Fi / 4G', 'Wi-Fi / 4G'],
                'ttl'    => ['TTL / 12-pin (o\'rnatiladigan)', 'TTL / 12-pin (встраиваемый)', 'TTL / 12-pin (embedded)'],
            ]],
        'form-factor' => [
            ['uz' => 'Korpus turi', 'ru' => 'Форм-фактор', 'en' => 'Form factor'], false, [
                'handheld' => ["Qo'l skaneri", 'Ручной', 'Handheld'],
                'pocket'   => ["Cho'ntak skaneri", 'Карманный', 'Pocket'],
                'desktop'  => ['Stol usti (prezentatsion)', 'Настольный (презентационный)', 'Desktop / presentation'],
                'wearable' => ["Kiyiladigan (barmoq / qo'lqop)", 'Носимый (на палец / перчатку)', 'Wearable (finger / glove)'],
                'fixed'    => ["Statsionar o'rnatiladigan", 'Стационарный встраиваемый', 'Fixed mount'],
                'module'   => ['Skaner moduli (OEM)', 'Сканирующий модуль (OEM)', 'Scan engine module (OEM)'],
                'pda'      => ["Ma'lumot yig'ish terminali (TSD)", 'Терминал сбора данных (ТСД)', 'Mobile computer (PDA)'],
            ]],
        'reads-screen' => [
            ['uz' => "Telefon ekranidan o'qiydi", 'ru' => 'Читает с экрана телефона', 'en' => 'Reads from phone screens'], false, [
                'yes' => ['Ha', 'Да', 'Yes'],
                'no'  => ["Yo'q", 'Нет', 'No'],
            ]],
        'resolution' => [
            ['uz' => 'Sensor ruxsati', 'ru' => 'Разрешение сенсора', 'en' => 'Sensor resolution'], false, [
                '03mp' => ['0.3 MP (640×480)', '0.3 Мп (640×480)', '0.3 MP (640×480)'],
                '1mp'  => ['1 MP (1280×800)', '1 Мп (1280×800)', '1 MP (1280×800)'],
                '13mp' => ['1.3 MP (1280×1024)', '1.3 Мп (1280×1024)', '1.3 MP (1280×1024)'],
            ]],
        'features' => [
            ['uz' => 'Imkoniyatlar', 'ru' => 'Возможности', 'en' => 'Features'], true, [
                'ocr'     => ['Hujjatlarni OCR (pasport, ID)', 'OCR документов (паспорт, ID)', 'Document OCR (passport, ID)'],
                'dpm'     => ['DPM kodlar (metallda)', 'DPM-коды (на металле)', 'DPM codes (on metal)'],
                'rugged'  => ['Sanoat himoyasi', 'Промышленное исполнение', 'Industrial rugged'],
                'drop'    => ['Yiqilishga chidamli', 'Ударопрочный', 'Drop resistant'],
                'android' => ['Android', 'Android', 'Android'],
            ]],
    ];

    public function run(): void
    {
        /** @var array<string, array<string, int>> $ids attribute slug => value key => value id */
        $ids = [];

        foreach (array_values(array_keys(self::ATTRIBUTES)) as $order => $slug) {
            [$name, $multiple, $values] = self::ATTRIBUTES[$slug];

            $attr = Attribute::updateOrCreate(['slug' => $slug], [
                'name' => $name, 'is_multiple' => $multiple, 'is_filterable' => true, 'is_active' => true, 'sort_order' => $order,
            ]);

            foreach (array_keys($values) as $i => $key) {
                [$uz, $ru, $en] = $values[$key];
                // Match an existing value by its English text so admin re-ordering/renaming in uz/ru survives.
                $value = $attr->values()->get()->first(fn (AttributeValue $v) => $v->getTranslation('value', 'en') === $en)
                    ?? $attr->values()->make();
                $value->fill(['sort_order' => $i, 'value' => ['uz' => $uz, 'ru' => $ru, 'en' => $en]])->save();
                $ids[$slug][$key] = $value->id;
            }
        }

        $assigned = 0;
        foreach (Product::with(['categories', 'attributeValues'])->get() as $product) {
            foreach ($this->detect($product) as $slug => $keys) {
                $attrValueIds = array_values($ids[$slug]);
                $hasAny = $product->attributeValues->contains(fn ($v) => in_array($v->id, $attrValueIds, true));
                if ($hasAny || ! $keys) {
                    continue;
                }
                $product->attributeValues()->attach(array_map(fn ($k) => $ids[$slug][$k], array_unique($keys)));
                $assigned++;
            }
        }

        cache()->forget('bot_catalog_context');
        $this->command?->info("Attributes seeded; {$assigned} product/attribute assignments added.");
    }

    /** @return array<string, string[]> attribute slug => value keys */
    private function detect(Product $p): array
    {
        // Product names are just model codes; the short description holds the type ("Laser Wireless Barcode Scanner").
        $name = $p->getTranslation('short_description', 'en') . ' ' . $p->getTranslation('name', 'en') . ' ' . $p->sku;
        $cats = $p->categories->pluck('slug')->all();
        $text = html_entity_decode(strip_tags(implode(' ', [
            $name,
            $p->getTranslation('short_description', 'en'),
            $p->getTranslation('description', 'en'),
        ])));
        $has = fn (string $re) => (bool) preg_match($re, $text);
        $in = fn (string $slug) => in_array($slug, $cats, true);

        // Scan engine
        $engine = match (true) {
            (bool) preg_match('/laser/i', $name) => 'laser',
            (bool) preg_match('/\bCCD\b/i', $name) => 'ccd',
            $has('/\b2D\b|QR|DataMatrix|Dotcode|1D\s*(\/|and)\s*2D|image sensor|CMOS|\bDPM\b/i') || $in('data-collectors') => '2d',
            default => null,
        };

        // Connectivity
        $conn = [];
        if ($in('data-collectors')) {
            $conn = ['wifi4g', 'bt', 'usb'];
        } else {
            if ($in('wired-barcode-scanners') || $in('desktop-barcode-scanners') || $has('/\bUSB\b|Type-[BC]\s*cable/i')) $conn[] = 'usb';
            if ($has('/RS-?232/i')) $conn[] = 'rs232';
            if ($has('/\bBT\b|bluetooth/i')) $conn[] = 'bt';
            if ($has('/2\.4\s?G/i')) $conn[] = '24g';
            if ($has('/cradle/i')) $conn[] = 'cradle';
            if ($has('/12-pin|\bTTL\b/i')) $conn[] = 'ttl';
        }

        // Form factor (most specific first)
        $form = match (true) {
            $in('data-collectors') => 'pda',
            $in('wearable-barcode-scanners') => 'wearable',
            $in('barcode-scanner-modules') => preg_match('/fixed/i', $name) ? 'fixed' : 'module',
            $in('desktop-barcode-scanners') => 'desktop',
            (bool) preg_match('/pocket/i', $name) => 'pocket',
            default => 'handheld',
        };

        // Screen reading: every 2D imager in this catalog reads screens; 1D devices do not.
        $screen = match ($engine) {
            '2d' => 'yes',
            'laser', 'ccd' => 'no',
            default => null,
        };

        $resolution = match (true) {
            $has('/1280\s?[×x]\s?1024|1\.3\s?MP/i') => '13mp',
            $has('/\b1(\.0)?\s?MP\b|1280\s?[×x]\s?800/i') => '1mp',
            $has('/0\.3\s?MP|640\s?[×x]\s?480/i') => '03mp',
            default => null,
        };

        $features = [];
        if ($has('/\bOCR\b/')) $features[] = 'ocr';
        if ($has('/\bDPM\b/')) $features[] = 'dpm';
        if ($in('industrial-barcode-scanners') || preg_match('/industrial/i', $name)) $features[] = 'rugged';
        if ($has('/\d(\.\d)?\s?m\s+drop|drop[- ]resist/i')) $features[] = 'drop';
        if ($has('/android/i')) $features[] = 'android';

        return array_filter([
            'scan-engine'  => array_filter([$engine]),
            'connectivity' => $conn,
            'form-factor'  => [$form],
            'reads-screen' => array_filter([$screen]),
            'resolution'   => array_filter([$resolution]),
            'features'     => $features,
        ]);
    }
}
