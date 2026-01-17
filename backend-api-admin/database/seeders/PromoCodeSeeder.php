<?php

namespace Database\Seeders;

use App\Enums\DiscountType;
use App\Models\PromoCode;
use Illuminate\Database\Seeder;

class PromoCodeSeeder extends Seeder
{
    public function run(): void
    {
        $promoCodes = [
            [
                'code' => 'WELCOME2024',
                'description' => 'Diskon 20% untuk pengguna baru',
                'discount_type' => DiscountType::PERCENTAGE,
                'discount_value' => 20,
                'min_purchase_amount' => 100000,
                'max_discount_amount' => 100000,
                'usage_limit' => 100,
                'usage_count' => 15,
                'start_date' => now()->subMonth(),
                'end_date' => now()->addMonths(2),
                'is_active' => true,
            ],
            [
                'code' => 'GURU50K',
                'description' => 'Potongan Rp 50.000 untuk semua kursus',
                'discount_type' => DiscountType::FIXED,
                'discount_value' => 50000,
                'min_purchase_amount' => 200000,
                'max_discount_amount' => null,
                'usage_limit' => 50,
                'usage_count' => 8,
                'start_date' => now()->subDays(15),
                'end_date' => now()->addMonth(),
                'is_active' => true,
            ],
            [
                'code' => 'RAMADAN30',
                'description' => 'Diskon 30% spesial Ramadan',
                'discount_type' => DiscountType::PERCENTAGE,
                'discount_value' => 30,
                'min_purchase_amount' => 150000,
                'max_discount_amount' => 150000,
                'usage_limit' => 200,
                'usage_count' => 45,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'is_active' => true,
            ],
            [
                'code' => 'EXPIRED',
                'description' => 'Kode promo yang sudah kadaluarsa',
                'discount_type' => DiscountType::PERCENTAGE,
                'discount_value' => 15,
                'min_purchase_amount' => 50000,
                'max_discount_amount' => null,
                'usage_limit' => 10,
                'usage_count' => 10,
                'start_date' => now()->subMonths(2),
                'end_date' => now()->subMonth(),
                'is_active' => false,
            ],
        ];

        foreach ($promoCodes as $promoCode) {
            PromoCode::create($promoCode);
        }
    }
}
