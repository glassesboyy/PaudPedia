<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereHas('roles')->get();

        if ($users->isEmpty()) {
            return;
        }

        $testimonials = [
            [
                'content' => 'Platform PaudPedia sangat membantu saya dalam mengelola administrasi sekolah. Fitur-fiturnya lengkap dan mudah digunakan!',
                'rating' => 5,
                'is_featured' => true,
            ],
            [
                'content' => 'Kursus-kursus di PaudPedia sangat bermanfaat. Saya jadi lebih percaya diri dalam mengajar anak-anak.',
                'rating' => 5,
                'is_featured' => true,
            ],
            [
                'content' => 'Sistem absensi dan penilaian sangat memudahkan. Orang tua juga lebih mudah memantau perkembangan anak.',
                'rating' => 4,
                'is_featured' => true,
            ],
            [
                'content' => 'Webinar-webinar yang disediakan berkualitas tinggi dengan mentor-mentor berpengalaman.',
                'rating' => 5,
                'is_featured' => false,
            ],
            [
                'content' => 'Produk digital seperti e-book dan template sangat membantu pekerjaan sehari-hari saya sebagai guru.',
                'rating' => 4,
                'is_featured' => false,
            ],
        ];

        foreach ($testimonials as $index => $testimonialData) {
            $user = $users->random();
            
            Testimonial::create([
                ...$testimonialData,
                'user_id' => $user->id,
                'name' => $user->name,
                'title' => fake()->randomElement([
                    'Guru TK',
                    'Kepala Sekolah',
                    'Orang Tua Siswa',
                    'Praktisi PAUD',
                ]),
                'photo_url' => null,
                'is_approved' => true,
            ]);
        }
    }
}
