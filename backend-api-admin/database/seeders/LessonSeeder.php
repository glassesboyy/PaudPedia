<?php

namespace Database\Seeders;

use App\Enums\ContentType;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        $modules = Module::all();

        if ($modules->isEmpty()) {
            return;
        }

        foreach ($modules as $module) {
            $lessonCount = fake()->numberBetween(3, 8);

            for ($i = 1; $i <= $lessonCount; $i++) {
                $contentType = fake()->randomElement([
                    ContentType::VIDEO,
                    ContentType::VIDEO,
                    ContentType::VIDEO,
                    ContentType::PDF,
                    ContentType::QUIZ,
                ]);

                Lesson::create([
                    'module_id' => $module->id,
                    'title' => "Pelajaran {$i}: " . fake()->sentence(4),
                    'content_type' => $contentType,
                    'content_url' => $this->getContentUrl($contentType),
                    'duration_minutes' => fake()->numberBetween(10, 60),
                    'order' => $i,
                    'is_preview' => $i === 1, // First lesson is preview
                ]);
            }
        }
    }

    private function getContentUrl(ContentType $type): string
    {
        return match($type) {
            ContentType::VIDEO => 'https://youtube.com/watch?v=' . fake()->lexify('???????????'),
            ContentType::PDF => 'https://example.com/pdfs/' . fake()->slug() . '.pdf',
            ContentType::QUIZ => 'https://example.com/quizzes/' . fake()->slug(),
            default => 'https://example.com/content',
        };
    }
}
