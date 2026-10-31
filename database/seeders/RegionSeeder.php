<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            ['slug' => 'tashkent-city', 'name' => ['uz' => 'Toshkent shahri', 'ru' => 'г. Ташкент', 'en' => 'Tashkent City']],
            ['slug' => 'tashkent-region', 'name' => ['uz' => 'Toshkent viloyati', 'ru' => 'Ташкентская область', 'en' => 'Tashkent Region']],
            ['slug' => 'andijan', 'name' => ['uz' => 'Andijon', 'ru' => 'Андижан', 'en' => 'Andijan']],
            ['slug' => 'fergana', 'name' => ['uz' => 'Farg\'ona', 'ru' => 'Фергона', 'en' => 'Fergana']],
            ['slug' => 'namangan', 'name' => ['uz' => 'Namangan', 'ru' => 'Наманган', 'en' => 'Namangan']],
            ['slug' => 'samarkand', 'name' => ['uz' => 'Samarqand', 'ru' => 'Самарканд', 'en' => 'Samarkand']],
            ['slug' => 'bukhara', 'name' => ['uz' => 'Buxoro', 'ru' => 'Бухара', 'en' => 'Bukhara']],
            ['slug' => 'khorezm', 'name' => ['uz' => 'Xorazm', 'ru' => 'Хорезм', 'en' => 'Khorezm']],
            ['slug' => 'kashkadarya', 'name' => ['uz' => 'Qashqadaryo', 'ru' => 'Кашкадарья', 'en' => 'Kashkadarya']],
            ['slug' => 'surkhandarya', 'name' => ['uz' => 'Surxondaryo', 'ru' => 'Сурхандарья', 'en' => 'Surkhandarya']],
            ['slug' => 'jizzakh', 'name' => ['uz' => 'Jizzax', 'ru' => 'Джизак', 'en' => 'Jizzakh']],
            ['slug' => 'sirdaria', 'name' => ['uz' => 'Sirdaryo', 'ru' => 'Сырдарья', 'en' => 'Sirdaria']],
            ['slug' => 'navoi', 'name' => ['uz' => 'Navoiy', 'ru' => 'Навои', 'en' => 'Navoi']],
            ['slug' => 'karakalpakstan', 'name' => ['uz' => 'Qoraqalpog\'iston', 'ru' => 'Каракалпакстан', 'en' => 'Karakalpakstan']],
        ];

        foreach ($regions as $region) {
            \App\Models\Region::create($region);
        }
    }
}
