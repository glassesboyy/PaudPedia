<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $modules = Module::all();

        if ($modules->isEmpty()) {
            return;
        }

        // Add quiz to approximately 60% of modules
        foreach ($modules as $module) {
            if (fake()->boolean(60)) {
                $this->createQuizForModule($module);
            }
        }
    }

    private function createQuizForModule(Module $module): void
    {
        $quiz = Quiz::create([
            'module_id' => $module->id,
            'title' => 'Quiz: ' . $module->title,
            'description' => fake()->optional()->paragraph(),
        ]);

        // Create 3-7 questions per quiz
        $questionCount = fake()->numberBetween(3, 7);

        for ($q = 1; $q <= $questionCount; $q++) {
            $this->createQuestion($quiz);
        }
    }

    private function createQuestion(Quiz $quiz): void
    {
        $question = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => $this->generateQuestion(),
        ]);

        // Create 3-5 answers per question
        $answerCount = fake()->numberBetween(3, 5);
        $correctAnswerIndex = fake()->numberBetween(1, $answerCount);

        for ($a = 1; $a <= $answerCount; $a++) {
            QuizAnswer::create([
                'quiz_question_id' => $question->id,
                'answer' => $this->generateAnswer(),
                'is_correct' => ($a === $correctAnswerIndex),
            ]);
        }
    }

    private function generateQuestion(): string
    {
        $templates = [
            'Apa yang dimaksud dengan %s?',
            'Manakah pernyataan yang benar tentang %s?',
            'Bagaimana cara %s?',
            'Apa tujuan dari %s?',
            'Kapan sebaiknya kita %s?',
            'Mengapa %s penting dalam pembelajaran anak?',
            'Apa manfaat dari %s?',
            'Siapa yang berperan dalam %s?',
            'Bagaimana pengaruh %s terhadap perkembangan anak?',
            'Apa yang perlu diperhatikan saat %s?',
        ];

        $topics = [
            'perkembangan motorik halus',
            'stimulasi kognitif',
            'bermain sambil belajar',
            'pengembangan bahasa',
            'interaksi sosial',
            'kreativitas anak',
            'kemandirian anak',
            'pola asuh positif',
            'pendidikan karakter',
            'kecerdasan emosional',
            'pembelajaran aktif',
            'media pembelajaran',
            'evaluasi perkembangan',
            'lingkungan belajar',
            'komunikasi efektif',
        ];

        return sprintf(
            fake()->randomElement($templates),
            fake()->randomElement($topics)
        );
    }

    private function generateAnswer(): string
    {
        return fake()->sentence(fake()->numberBetween(5, 15));
    }
}
