<?php

namespace Database\Seeders;

use App\Models\Mentor;
use App\Models\Webinar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WebinarSeeder extends Seeder
{
    public function run(): void
    {
        $mentors = Mentor::all();

        if ($mentors->isEmpty()) {
            return;
        }

        $webinars = [
            [
                'title' => 'Strategi Efektif Mengajar PAUD di Era Digital',
                'description' => 'Pelajari strategi terbaru dalam mengajar anak usia dini dengan memanfaatkan teknologi digital yang tepat.',
                'price' => 150000,
                'original_price' => 200000,
                'duration_minutes' => 120,
                'max_participants' => 100,
                'scheduled_at' => now()->addDays(7)->setTime(14, 0),
            ],
            [
                'title' => 'Memahami Psikologi Anak Usia Dini',
                'description' => 'Webinar mendalam tentang perkembangan psikologi anak dan cara mengoptimalkannya.',
                'price' => 100000,
                'original_price' => 150000,
                'duration_minutes' => 90,
                'max_participants' => 150,
                'scheduled_at' => now()->addDays(14)->setTime(19, 0),
            ],
            [
                'title' => 'Kreativitas dalam Pembelajaran PAUD',
                'description' => 'Kembangkan kreativitas anak melalui metode pembelajaran yang menyenangkan.',
                'price' => 125000,
                'original_price' => null,
                'duration_minutes' => 100,
                'max_participants' => 80,
                'scheduled_at' => now()->addDays(21)->setTime(10, 0),
            ],
        ];

        foreach ($webinars as $webinarData) {
            Webinar::create([
                ...$webinarData,
                'mentor_id' => $mentors->random()->id,
                'slug' => Str::slug($webinarData['title']),
                'thumbnail_url' => null,
                'zoom_link' => 'https://zoom.us/j/' . fake()->numerify('##########'),
                'zoom_meeting_id' => fake()->numerify('### #### ####'),
                'zoom_passcode' => fake()->numerify('######'),
                'is_active' => true,
            ]);
        }
    }
}
