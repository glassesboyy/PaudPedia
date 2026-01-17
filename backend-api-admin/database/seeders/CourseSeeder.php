<?php

namespace Database\Seeders;

use App\Enums\CourseLevel;
use App\Models\Category;
use App\Models\Course;
use App\Models\Mentor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $mentors = Mentor::all();
        $categories = Category::where('type', 'course')->get();

        if ($mentors->isEmpty() || $categories->isEmpty()) {
            return;
        }

        $courses = [
            [
                'title' => 'Dasar-dasar Pendidikan Anak Usia Dini',
                'description' => 'Kursus komprehensif tentang fondasi PAUD yang efektif untuk guru pemula.',
                'price' => 500000,
                'original_price' => 750000,
                'level' => CourseLevel::BEGINNER,
                'duration_hours' => 20,
            ],
            [
                'title' => 'Manajemen Kelas PAUD yang Efektif',
                'description' => 'Pelajari teknik manajemen kelas yang terbukti meningkatkan pembelajaran anak.',
                'price' => 600000,
                'original_price' => null,
                'level' => CourseLevel::INTERMEDIATE,
                'duration_hours' => 15,
            ],
            [
                'title' => 'Psikologi Perkembangan Anak',
                'description' => 'Memahami tahapan perkembangan anak dan cara mengoptimalkannya.',
                'price' => 750000,
                'original_price' => 1000000,
                'level' => CourseLevel::ADVANCED,
                'duration_hours' => 30,
            ],
            [
                'title' => 'Seni dan Kreativitas untuk PAUD',
                'description' => 'Kembangkan kreativitas anak melalui seni dan aktivitas kreatif.',
                'price' => 450000,
                'original_price' => null,
                'level' => CourseLevel::BEGINNER,
                'duration_hours' => 12,
            ],
        ];

        foreach ($courses as $courseData) {
            Course::create([
                ...$courseData,
                'mentor_id' => $mentors->random()->id,
                'category_id' => $categories->random()->id,
                'slug' => Str::slug($courseData['title']),
                'thumbnail_url' => null,
                'is_published' => true,
            ]);
        }
    }
}
