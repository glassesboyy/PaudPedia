<?php

namespace Database\Seeders;

use App\Enums\AssessmentScale;
use App\Enums\Semester;
use App\Models\Assessment;
use App\Models\Student;
use App\Models\DevelopmentProgram;
use App\Models\DevelopmentIndicator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            'Nilai Agama dan Moral' => [
                'Terbiasa mengucapkan kata-kata pujian ketika melihat ciptaan Tuhan',
                'Terbiasa merawat tanaman, binatang, dan benda-benda di sekitar',
                'Terbiasa berdoa sebelum dan sesudah kegiatan',
            ],
            'Fisik Motorik' => [
                'Mampu menggunakan otot besar untuk melompat dan berlari',
                'Mampu menggunakan otot kecil untuk menggambar',
            ],
            'Kognitif' => [
                'Mampu mengenal warna dasar',
                'Mampu berhitung 1 sampai 10',
            ],
            'Bahasa' => [
                'Mampu menjawab pertanyaan sederhana',
                'Mampu menceritakan kembali cerita pendek',
            ],
            'Sosial Emosional' => [
                'Bisa bermain bersama teman dengan rukun',
                'Mampu mengantre dan menunggu giliran',
            ],
            'Seni' => [
                'Bisa menyanyikan lagu anak-anak',
                'Mewarnai gambar dengan rapi',
            ],
        ];

        $indicatorIds = [];
        $orderProg = 1;
        foreach ($programs as $progName => $indicators) {
            $progId = DevelopmentProgram::insertGetId([
                'name' => $progName,
                'order' => $orderProg++,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $orderInd = 1;
            foreach ($indicators as $indName) {
                $indId = DevelopmentIndicator::insertGetId([
                    'program_id' => $progId,
                    'name' => $indName,
                    'order' => $orderInd++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $indicatorIds[] = $indId;
            }
        }

        $students = Student::all();
        
        foreach ($students as $student) {
            foreach ([Semester::SEMESTER_1, Semester::SEMESTER_2] as $semester) {
                // Generate 6 months for each semester
                $baseDate = $semester === Semester::SEMESTER_1 ? Carbon::create(2024, 7, 1) : Carbon::create(2025, 1, 1);
                
                for ($m = 0; $m < 6; $m++) {
                    $monthStr = $baseDate->copy()->addMonths($m)->format('Y-m');
                    
                    foreach ($indicatorIds as $indId) {
                        Assessment::create([
                            'student_id' => $student->id,
                            'indicator_id' => $indId,
                            'assessment_month' => $monthStr,
                            'scale' => fake()->randomElement([
                                AssessmentScale::BB,
                                AssessmentScale::MB,
                                AssessmentScale::BSH,
                                AssessmentScale::BSH,
                                AssessmentScale::BSB,
                            ]),
                            'semester' => $semester,
                            'academic_year' => '2024/2025',
                            'notes' => fake()->boolean(20) ? fake()->sentence() : null,
                            'assessed_at' => $baseDate->copy()->addMonths($m)->endOfMonth(),
                        ]);
                    }
                }
            }
        }
    }
}
