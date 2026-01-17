<?php

namespace Database\Seeders;

use App\Enums\SubscriptionPlan;
use App\Models\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $schools = [
            [
                'name' => 'TK Harapan Bangsa',
                'npsn' => '12345678',
                'address' => 'Jl. Pendidikan No. 123, Jakarta Selatan',
                'phone' => '021-12345678',
                'email' => 'info@tkharapanbangsa.sch.id',
                'logo_url' => null,
                'subscription_plan' => SubscriptionPlan::PRO,
                'subscription_started_at' => now()->subMonths(6),
                'subscription_ended_at' => now()->addMonths(6),
                'is_active' => true,
            ],
            [
                'name' => 'TK Ceria Cendekia',
                'npsn' => '23456789',
                'address' => 'Jl. Raya Bogor KM 25, Bogor',
                'phone' => '0251-1234567',
                'email' => 'info@ceriacendekia.sch.id',
                'logo_url' => null,
                'subscription_plan' => SubscriptionPlan::FREE,
                'subscription_started_at' => now()->subMonths(2),
                'subscription_ended_at' => null,
                'is_active' => true,
            ],
            [
                'name' => 'TK Mutiara Hati',
                'npsn' => '34567890',
                'address' => 'Jl. Ahmad Yani No. 45, Bandung',
                'phone' => '022-7654321',
                'email' => 'admin@mutiarahati.sch.id',
                'logo_url' => null,
                'subscription_plan' => SubscriptionPlan::PRO,
                'subscription_started_at' => now()->subYear(),
                'subscription_ended_at' => now()->addYear(),
                'is_active' => true,
            ],
        ];

        foreach ($schools as $school) {
            School::create($school);
        }
    }
}
