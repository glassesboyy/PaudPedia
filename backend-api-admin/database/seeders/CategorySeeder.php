<?php

namespace Database\Seeders;

use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Course categories
            ['name' => 'Pengembangan Anak', 'type' => CategoryType::COURSE],
            ['name' => 'Manajemen Kelas', 'type' => CategoryType::COURSE],
            ['name' => 'Kreativitas & Seni', 'type' => CategoryType::COURSE],
            ['name' => 'Teknologi Pendidikan', 'type' => CategoryType::COURSE],
            ['name' => 'Psikologi Anak', 'type' => CategoryType::COURSE],

            // Product categories
            ['name' => 'E-Book', 'type' => CategoryType::PRODUCT],
            ['name' => 'Template', 'type' => CategoryType::PRODUCT],
            ['name' => 'Media Pembelajaran', 'type' => CategoryType::PRODUCT],
            ['name' => 'Modul', 'type' => CategoryType::PRODUCT],

            // Article categories
            ['name' => 'Tips & Trik', 'type' => CategoryType::ARTICLE],
            ['name' => 'Berita Pendidikan', 'type' => CategoryType::ARTICLE],
            ['name' => 'Studi Kasus', 'type' => CategoryType::ARTICLE],
            ['name' => 'Panduan', 'type' => CategoryType::ARTICLE],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => 'Kategori untuk ' . strtolower($category['name']),
                'type' => $category['type'],
            ]);
        }
    }
}
