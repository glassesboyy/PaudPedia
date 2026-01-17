<?php

namespace Database\Seeders;

use App\Models\Mentor;
use Illuminate\Database\Seeder;

class MentorSeeder extends Seeder
{
    public function run(): void
    {
        $mentors = [
            [
                'name' => 'Dr. Siti Nurhaliza, M.Pd',
                'title' => 'Doktor Pendidikan Anak Usia Dini',
                'bio' => 'Pakar pendidikan PAUD dengan pengalaman lebih dari 15 tahun dalam mengembangkan kurikulum dan metode pembelajaran inovatif.',
                'expertise' => 'Pengembangan Kurikulum PAUD, Psikologi Anak',
                'social_media' => [
                    'instagram' => '@dr.siti.nurhaliza',
                    'linkedin' => 'siti-nurhaliza',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Prof. Ahmad Dahlan, M.Pd',
                'title' => 'Profesor Psikologi Pendidikan',
                'bio' => 'Peneliti dan praktisi psikologi anak dengan fokus pada perkembangan kognitif dan emosional anak usia dini.',
                'expertise' => 'Psikologi Anak, Perkembangan Kognitif',
                'social_media' => [
                    'instagram' => '@prof.ahmad',
                    'youtube' => 'Prof Ahmad Dahlan',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Ibu Dewi Kusuma, S.Pd',
                'title' => 'Praktisi PAUD Berpengalaman',
                'bio' => 'Guru PAUD dengan 10+ tahun pengalaman dalam mengajar dan mengelola TK. Spesialis dalam kreativitas dan seni anak.',
                'expertise' => 'Seni & Kreativitas, Manajemen Kelas',
                'social_media' => [
                    'instagram' => '@ibudewi.paud',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($mentors as $mentor) {
            Mentor::create($mentor);
        }
    }
}
