<?php

namespace Database\Seeders;

use App\Enums\AssessmentScale;
use App\Enums\Semester;
use App\Models\Assessment;
use App\Models\Student;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        
        $aspects = [
            'Nilai Agama dan Moral',
            'Fisik-Motorik',
            'Kognitif',
            'Bahasa',
            'Sosial-Emosional',
            'Seni',
        ];

        foreach ($students as $student) {
            // Create assessments for both semesters
            foreach ([Semester::SEMESTER_1, Semester::SEMESTER_2] as $semester) {
                foreach ($aspects as $aspect) {
                    Assessment::create([
                        'student_id' => $student->id,
                        'aspect' => $aspect,
                        'description' => $this->getDescriptionForAspect($aspect),
                        'scale' => fake()->randomElement([
                            AssessmentScale::BB,
                            AssessmentScale::MB,
                            AssessmentScale::BSH,
                            AssessmentScale::BSH,
                            AssessmentScale::BSB,
                        ]),
                        'semester' => $semester,
                        'academic_year' => '2024/2025',
                        'notes' => fake()->boolean(30) ? fake()->sentence() : null,
                        'assessed_at' => now(),
                    ]);
                }
            }
        }
    }

    private function getDescriptionForAspect(string $aspect): string
    {
        return match($aspect) {
            'Nilai Agama dan Moral' => 'Mampu berdoa sebelum dan sesudah kegiatan',
            'Fisik-Motorik' => 'Mampu melakukan gerakan dasar',
            'Kognitif' => 'Mampu mengenal warna, bentuk, dan angka',
            'Bahasa' => 'Mampu berkomunikasi dengan baik',
            'Sosial-Emosional' => 'Mampu berinteraksi dengan teman sebaya',
            'Seni' => 'Mampu mengekspresikan diri melalui seni',
            default => 'Penilaian aspek perkembangan anak',
        };
    }
}
