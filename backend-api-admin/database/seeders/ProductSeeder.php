<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::where('type', 'product')->get();

        if ($categories->isEmpty()) {
            return;
        }

        $products = [
            [
                'title' => 'E-Book Panduan Guru PAUD Profesional',
                'description' => 'Panduan lengkap menjadi guru PAUD yang profesional dan berdampak.',
                'price' => 75000,
                'original_price' => 100000,
            ],
            [
                'title' => 'Template RPP PAUD Kurikulum Merdeka',
                'description' => 'Kumpulan template RPP siap pakai sesuai kurikulum merdeka.',
                'price' => 50000,
                'original_price' => null,
            ],
            [
                'title' => 'Media Pembelajaran Interaktif - Angka & Huruf',
                'description' => 'Paket media pembelajaran digital untuk mengenal angka dan huruf.',
                'price' => 125000,
                'original_price' => 175000,
            ],
            [
                'title' => 'Modul Pengembangan Karakter Anak',
                'description' => 'Modul lengkap untuk mengembangkan karakter positif anak usia dini.',
                'price' => 60000,
                'original_price' => null,
            ],
        ];

        foreach ($products as $productData) {
            Product::create([
                ...$productData,
                'category_id' => $categories->random()->id,
                'slug' => Str::slug($productData['title']),
                'thumbnail_url' => null,
                'file_url' => null,
                'is_active' => true,
            ]);
        }
    }
}
