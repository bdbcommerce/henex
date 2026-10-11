<?php

namespace Database\Seeders;

use App\Models\Slide;
use Database\Seeders\Concerns\AttachesSeedMedia;
use Illuminate\Database\Seeder;

class SlideSeeder extends Seeder
{
    use AttachesSeedMedia;

    /**
     * Homepage hero slides. 'image' is a file name inside database/seeders/media/slides/.
     * A slide whose image file is missing is skipped, so the hero keeps its built-in
     * fallback slides until real images are dropped in.
     */
    public function run(): void
    {
        $slides = [
            [
                'image' => 'slide-1.jpg',
                'link' => '/uz/products',
                'title' => ['uz' => 'HENEX shtrix-kod skanerlari', 'ru' => 'Сканеры штрих-кода HENEX', 'en' => 'HENEX barcode scanners'],
                'subtitle' => ['uz' => "O'zbekistondagi rasmiy distribyutor", 'ru' => 'Официальный дистрибьютор в Узбекистане', 'en' => 'Official distributor in Uzbekistan'],
                'button_text' => ['uz' => 'Mahsulotlar', 'ru' => 'Каталог', 'en' => 'Products'],
            ],
            [
                'image' => 'slide-2.jpg',
                'link' => '/uz/where-to-buy',
                'title' => ['uz' => 'Hududingizda xarid qiling', 'ru' => 'Купить в вашем регионе', 'en' => 'Buy in your region'],
                'subtitle' => ['uz' => "Barcha 14 hududda sotuvchilar", 'ru' => 'Дилеры во всех 14 регионах', 'en' => 'Resellers in all 14 regions'],
                'button_text' => ['uz' => 'Qayerdan sotib olish', 'ru' => 'Где купить', 'en' => 'Where to buy'],
            ],
        ];

        foreach ($slides as $i => $data) {
            $image = $data['image'];
            unset($data['image']);

            if (! is_file($this->mediaPath("slides/{$image}"))) {
                $this->command?->warn("  Skipped slide: media/slides/{$image} not found");
                continue;
            }

            $slide = Slide::create($data + ['sort_order' => $i, 'is_active' => true]);
            $this->attachSeedMedia($slide, 'slide', 'slides', $image, prefix: 'hero-');
        }
    }
}
