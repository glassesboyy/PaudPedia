<?php

namespace Database\Seeders;

use App\Models\ParentProfile;
use App\Models\School;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $schools = School::with('classes')->get();

        foreach ($schools as $school) {

            $parents = ParentProfile::where('school_id', $school->id)->get();
            $classes = $school->classes;

            if ($classes->isEmpty() || $parents->isEmpty()) {
                continue;
            }

            foreach ($classes as $class) {
                $studentCount = min($class->capacity ?? 10, 10);

                for ($i = 0; $i < $studentCount; $i++) {
                    $gender = fake()->randomElement(['male', 'female']);
                    $parent = $parents->random();

                    Student::create([
                        'school_id' => $school->id,
                        'class_id' => $class->id,
                        'parent_profile_id' => $parent->id,
                        'name' => fake()->name($gender),
                        'nisn' => fake()->optional()->numerify('##########'), // 10 digit NISN
                        'birth_date' => fake()->dateTimeBetween('-6 years', '-4 years')->format('Y-m-d'),
                        'gender' => $gender,
                        'address' => fake()->address(),
                        'photo_url' => null,
                        'enrollment_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
                        'status' => 'active',
                    ]);
                }
            }
        }
    }
}
