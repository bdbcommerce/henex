<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            BrandAssetSeeder::class,
            RegionSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            SlideSeeder::class,
            AttributeSeeder::class,
            IndustrySeeder::class,
            KnowledgeBaseSeeder::class,
        ]);
    }
}
