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
                // Randomize content type: 60% video, 20% PDF, 20% text
                $contentType = fake()->randomElement([
                    ContentType::VIDEO,
                    ContentType::VIDEO,
                    ContentType::VIDEO,
                    ContentType::PDF,
                    ContentType::TEXT,
                ]);

                $lessonData = [
                    'module_id' => $module->id,
                    'title' => "Pelajaran {$i}: " . fake()->sentence(4),
                    'content_type' => $contentType,
                    'duration_minutes' => fake()->numberBetween(10, 60),
                    'order' => $i,
                    'video_url' => null,
                    'pdf_file' => null,
                    'text_content' => null,
                ];

                // Set content based on type
                match ($contentType) {
                    ContentType::VIDEO => $lessonData['video_url'] = $this->getVideoUrl(),
                    ContentType::PDF => $lessonData['pdf_file'] = $this->getPdfPath(),
                    ContentType::TEXT => $lessonData['text_content'] = $this->getTextContent(),
                };

                Lesson::create($lessonData);
            }
        }
    }

    private function getVideoUrl(): string
    {
        // Sample YouTube video URLs
        $videoIds = [
            'dQw4w9WgXcQ',
            'jNQXAC9IVRw',
            'oHg5SJYRHA0',
            '9bZkp7q19f0',
            'kJQP7kiw5Fk',
        ];

        return 'https://www.youtube.com/watch?v=' . fake()->randomElement($videoIds);
    }

    private function getPdfPath(): string
    {
        // Return a sample PDF file path (would be stored in storage)
        return 'lessons/pdfs/' . fake()->slug() . '.pdf';
    }

    private function getTextContent(): string
    {
        // Generate rich text content
        $paragraphs = fake()->paragraphs(fake()->numberBetween(3, 6));

        $content = '<h2>' . fake()->sentence(4) . '</h2>';

        foreach ($paragraphs as $paragraph) {
            $content .= '<p>' . $paragraph . '</p>';
        }

        // Add some formatting
        $content .= '<h3>Poin Penting</h3>';
        $content .= '<ul>';
        for ($i = 0; $i < fake()->numberBetween(3, 5); $i++) {
            $content .= '<li>' . fake()->sentence() . '</li>';
        }
        $content .= '</ul>';

        return $content;
    }
}
