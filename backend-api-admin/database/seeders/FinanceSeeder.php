<?php

namespace Database\Seeders;

use App\Enums\FinanceType;
use App\Models\Finance;
use App\Models\Student;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::whereHas('school', function($query) {
            $query->where('subscription_plan', 'pro');
        })->get();

        if ($students->isEmpty()) {
            return;
        }

        foreach ($students as $student) {
            // Create SPP for last 6 months
            for ($i = 0; $i < 6; $i++) {
                $month = now()->subMonths($i);
                
                Finance::create([
                    'student_id' => $student->id,
                    'type' => FinanceType::SPP,
                    'amount' => 250000,
                    'description' => 'SPP Bulan ' . $month->translatedFormat('F Y'),
                    'month' => $month->format('Y-m'),
                    'is_paid' => fake()->boolean(80), // 80% paid
                    'paid_at' => fake()->boolean(80) ? $month->addDays(fake()->numberBetween(1, 10)) : null,
                ]);
            }

            // Create Tabungan records
            for ($i = 0; $i < 10; $i++) {
                Finance::create([
                    'student_id' => $student->id,
                    'type' => FinanceType::TABUNGAN,
                    'amount' => fake()->numberBetween(5000, 50000),
                    'description' => 'Setoran tabungan',
                    'month' => now()->subDays(fake()->numberBetween(1, 60))->format('Y-m'),
                    'is_paid' => true,
                    'paid_at' => now()->subDays(fake()->numberBetween(1, 60)),
                ]);
            }
        }
    }
}
