<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\School;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class ClassRoomSeeder extends Seeder
{
    public function run(): void
    {
        $schools = School::with('teachers')->get();

        foreach ($schools as $school) {
            $teachers = $school->teachers;
            
            if ($teachers->isEmpty()) {
                continue;
            }

            // Create classes for each school
            $classes = [
                ['name' => 'Kelas A1', 'level' => 'TK A', 'capacity' => 15, 'academic_year' => '2024/2025'],
                ['name' => 'Kelas A2', 'level' => 'TK A', 'capacity' => 15, 'academic_year' => '2024/2025'],
                ['name' => 'Kelas B1', 'level' => 'TK B', 'capacity' => 20, 'academic_year' => '2024/2025'],
                ['name' => 'Kelas B2', 'level' => 'TK B', 'capacity' => 20, 'academic_year' => '2024/2025'],
            ];

            foreach ($classes as $index => $classData) {
                ClassRoom::create([
                    'school_id' => $school->id,
                    'homeroom_teacher_id' => $teachers->get($index % $teachers->count())->id ?? null,
                    'name' => $classData['name'],
                    'level' => $classData['level'],
                    'capacity' => $classData['capacity'],
                    'academic_year' => $classData['academic_year']
                ]);
            }
        }
    }
}
