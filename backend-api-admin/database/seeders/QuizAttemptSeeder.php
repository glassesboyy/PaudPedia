<?php

namespace Database\Seeders;

use App\Models\CourseEnrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use Illuminate\Database\Seeder;

class QuizAttemptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all enrollments with their course quizzes
        $enrollments = CourseEnrollment::with(['course.modules.quiz.questions.answers'])->get();

        if ($enrollments->isEmpty()) {
            return;
        }

        foreach ($enrollments as $enrollment) {
            // Get all quizzes from all modules in this course
            $quizzes = $this->getQuizzesFromCourse($enrollment->course);

            if ($quizzes->isEmpty()) {
                continue;
            }

            // Create attempts for some quizzes (70% chance)
            foreach ($quizzes as $quiz) {
                if (fake()->boolean(70)) {
                    $this->createAttemptsForQuiz($quiz, $enrollment);
                }
            }
        }
    }

    /**
     * Get all quizzes from a course
     */
    private function getQuizzesFromCourse($course)
    {
        $quizzes = collect();

        if (!$course || !$course->modules) {
            return $quizzes;
        }

        foreach ($course->modules as $module) {
            if ($module->quiz && $module->quiz->isNotEmpty()) {
                foreach ($module->quiz as $quiz) {
                    if ($quiz->questions->isNotEmpty()) {
                        $quizzes->push($quiz);
                    }
                }
            }
        }

        return $quizzes;
    }

    /**
     * Create quiz attempts for a quiz
     */
    private function createAttemptsForQuiz(Quiz $quiz, CourseEnrollment $enrollment): void
    {
        // Determine number of attempts (1-3)
        $attemptCount = fake()->numberBetween(1, 3);

        for ($i = 0; $i < $attemptCount; $i++) {
            $this->createSingleAttempt($quiz, $enrollment, $i);
        }
    }

    /**
     * Create a single quiz attempt with answers
     */
    private function createSingleAttempt(Quiz $quiz, CourseEnrollment $enrollment, int $attemptIndex): void
    {
        $questions = $quiz->questions;
        $totalQuestions = $questions->count();

        if ($totalQuestions === 0) {
            return;
        }

        // Simulate different attempt scenarios
        // First attempt: 50-80% correct, subsequent attempts: 60-100% correct (improving)
        $minCorrect = $attemptIndex === 0 ? 0.5 : 0.6;
        $maxCorrect = $attemptIndex === 0 ? 0.8 : 1.0;
        $targetCorrectRate = fake()->randomFloat(2, $minCorrect, $maxCorrect);

        // Create the attempt
        $startedAt = now()
            ->subDays(fake()->numberBetween(1, 60))
            ->subHours(fake()->numberBetween(0, 23))
            ->addDays($attemptIndex * fake()->numberBetween(1, 7));

        // Quiz duration: 5-30 minutes
        $durationMinutes = fake()->numberBetween(5, 30);
        $completedAt = $startedAt->copy()->addMinutes($durationMinutes);

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $enrollment->user_id,
            'enrollment_id' => $enrollment->id,
            'score' => 0, // Will be calculated after
            'total_questions' => $totalQuestions,
            'percentage' => 0, // Will be calculated after
            'is_passed' => false, // Will be calculated after
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
        ]);

        // Create answers for each question
        $correctCount = 0;
        foreach ($questions as $question) {
            $isCorrect = $this->createAnswerForQuestion($attempt, $question, $targetCorrectRate);
            if ($isCorrect) {
                $correctCount++;
            }
        }

        // Update attempt with calculated score
        $percentage = ($correctCount / $totalQuestions) * 100;
        $isPassed = $percentage >= QuizAttempt::PASSING_PERCENTAGE;

        $attempt->update([
            'score' => $correctCount,
            'percentage' => round($percentage, 2),
            'is_passed' => $isPassed,
        ]);
    }

    /**
     * Create an answer for a question
     */
    private function createAnswerForQuestion(QuizAttempt $attempt, $question, float $targetCorrectRate): bool
    {
        $answers = $question->answers;

        if ($answers->isEmpty()) {
            return false;
        }

        $correctAnswer = $answers->firstWhere('is_correct', true);
        $incorrectAnswers = $answers->where('is_correct', false);

        // Determine if user answers correctly (based on target rate with some randomness)
        $shouldBeCorrect = fake()->boolean($targetCorrectRate * 100);

        if ($shouldBeCorrect && $correctAnswer) {
            $selectedAnswerId = $correctAnswer->id;
            $isCorrect = true;
        } elseif ($incorrectAnswers->isNotEmpty()) {
            $selectedAnswerId = $incorrectAnswers->random()->id;
            $isCorrect = false;
        } else {
            // If no incorrect answers, must choose correct
            $selectedAnswerId = $correctAnswer?->id;
            $isCorrect = true;
        }

        QuizAttemptAnswer::create([
            'quiz_attempt_id' => $attempt->id,
            'quiz_question_id' => $question->id,
            'selected_answer_id' => $selectedAnswerId,
            'is_correct' => $isCorrect,
        ]);

        return $isCorrect;
    }
}
