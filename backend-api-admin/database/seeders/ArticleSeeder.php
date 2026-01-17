<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::where('type', 'article')->get();
        $authors = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['super_admin', 'user']);
        })->get();

        if ($categories->isEmpty() || $authors->isEmpty()) {
            return;
        }

        $articles = [
            [
                'title' => '10 Tips Mengelola Kelas PAUD yang Efektif',
                'content' => $this->getArticleContent(),
                'excerpt' => 'Pelajari 10 tips praktis untuk mengelola kelas PAUD dengan lebih efektif dan menyenangkan.',
                'tags' => ['manajemen kelas', 'tips guru', 'paud'],
                'is_featured' => true,
            ],
            [
                'title' => 'Memahami Tahapan Perkembangan Anak Usia 4-6 Tahun',
                'content' => $this->getArticleContent(),
                'excerpt' => 'Panduan lengkap memahami tahapan perkembangan anak usia 4-6 tahun.',
                'tags' => ['perkembangan anak', 'psikologi', 'paud'],
                'is_featured' => true,
            ],
            [
                'title' => 'Cara Meningkatkan Kreativitas Anak Melalui Bermain',
                'content' => $this->getArticleContent(),
                'excerpt' => 'Kreativitas anak dapat ditingkatkan melalui permainan yang tepat.',
                'tags' => ['kreativitas', 'bermain', 'paud'],
                'is_featured' => false,
            ],
            [
                'title' => 'Pentingnya Komunikasi dengan Orang Tua Siswa',
                'content' => $this->getArticleContent(),
                'excerpt' => 'Membangun komunikasi efektif dengan orang tua untuk kesuksesan pendidikan anak.',
                'tags' => ['komunikasi', 'orang tua', 'guru'],
                'is_featured' => false,
            ],
        ];

        foreach ($articles as $articleData) {
            $article = Article::create([
                ...$articleData,
                'category_id' => $categories->random()->id,
                'author_id' => $authors->random()->id,
                'slug' => Str::slug($articleData['title']),
                'featured_image_url' => null,
                'reading_time_minutes' => fake()->numberBetween(3, 10),
                'view_count' => fake()->numberBetween(0, 1000),
                'is_published' => true,
                'published_at' => now()->subDays(fake()->numberBetween(1, 30)),
            ]);

            // Update reading time based on content
            $article->reading_time_minutes = $article->calculateReadingTime();
            $article->save();
        }
    }

    private function getArticleContent(): string
    {
        return implode("\n\n", [
            fake()->paragraph(5),
            fake()->paragraph(8),
            fake()->paragraph(6),
            fake()->paragraph(7),
            fake()->paragraph(5),
        ]);
    }
}
