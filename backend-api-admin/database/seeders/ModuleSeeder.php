<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::all();

        if ($courses->isEmpty()) {
            return;
        }

        foreach ($courses as $course) {
            $moduleCount = fake()->numberBetween(3, 6);

            for ($i = 1; $i <= $moduleCount; $i++) {
                Module::create([
                    'course_id' => $course->id,
                    'title' => "Modul {$i}: " . fake()->sentence(4),
                    'description' => fake()->paragraph(),
                    'order' => $i,
                ]);
            }
        }
    }
}
