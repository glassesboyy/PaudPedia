<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\BaseController;
use App\Models\CourseEnrollment;
use App\Models\Quiz;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LmsQuizController extends BaseController
{
    public function show(Request $request, string $courseSlug, Quiz $quiz): JsonResponse
    {
        $enrollment = $this->resolveEnrollment($request, $courseSlug);

        if (!$enrollment) {
            return $this->forbidden('Anda belum terdaftar pada kursus ini');
        }

        if ((int) $quiz->module?->course_id !== (int) $enrollment->course_id) {
            return $this->notFound('Kuis tidak ditemukan pada kursus ini');
        }

        // Load questions securely (do not expose is_correct in answers)
        $quiz->load(['questions' => function ($q) {
            $q->select('id', 'quiz_id', 'question');
            $q->with(['answers' => function ($a) {
                // omit 'is_correct' field
                $a->select('id', 'quiz_question_id', 'answer');
            }]);
        }]);

        $bestAttempt = $quiz->getUserBestAttempt($request->user()->id);
        $latestAttempt = $quiz->getUserLatestAttempt($request->user()->id);

        if ($latestAttempt) {
            $latestAttempt->load('answers');
        }

        return $this->success([
            'id' => 'quiz-' . $quiz->id,
            'slug' => 'quiz-' . $quiz->id,
            'quiz_id' => $quiz->id,
            'title' => $quiz->title,
            'description' => $quiz->description,
            'type' => 'quiz',
            'questions' => $quiz->questions,
            'total_questions' => $quiz->questions->count(),
            'best_score' => $bestAttempt ? (int) $bestAttempt->percentage : null,
            'latest_score' => $latestAttempt ? (int) $latestAttempt->percentage : null,
            'is_passed' => $quiz->hasUserPassed($request->user()->id),
            'attempt_count' => $quiz->getUserAttemptCount($request->user()->id),
            'duration_minutes' => 0,
            'latest_attempt' => $latestAttempt ? [
                'id' => $latestAttempt->id,
                'score' => (int) $latestAttempt->percentage,
                'is_passed' => $latestAttempt->is_passed,
                'answers' => $latestAttempt->answers->map(function ($ans) {
                    return [
                        'question_id' => $ans->quiz_question_id,
                        'selected_answer_id' => $ans->selected_answer_id,
                        'is_correct' => $ans->is_correct,
                        'correct_answer_id' => $ans->is_correct ? $ans->selected_answer_id : $ans->question->getCorrectAnswer()?->id,
                    ];
                })
            ] : null,
        ], 'Detail kuis berhasil dimuat');
    }

    public function submit(Request $request, string $courseSlug, Quiz $quiz): JsonResponse
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:quiz_questions,id',
            'answers.*.answer_id' => 'required|exists:quiz_answers,id',
        ]);

        $enrollment = $this->resolveEnrollment($request, $courseSlug);

        if (!$enrollment) {
            return $this->forbidden('Anda belum terdaftar pada kursus ini');
        }

        if ((int) $quiz->module?->course_id !== (int) $enrollment->course_id) {
            return $this->notFound('Kuis tidak ditemukan pada kursus ini');
        }

        try {
            DB::beginTransaction();

            $attempt = $quiz->startAttempt($request->user()->id, $enrollment->id);
            $correctCount = 0;

            $questions = $quiz->questions()->with('answers')->get()->keyBy('id');

            foreach ($request->answers as $clientAnswer) {
                $questionId = $clientAnswer['question_id'];
                $answerId = $clientAnswer['answer_id'];

                if (!isset($questions[$questionId])) continue;

                $qModel = $questions[$questionId];

                $selectedAnswer = $qModel->answers->firstWhere('id', $answerId);
                $isCorrect = $selectedAnswer && $selectedAnswer->is_correct;
                
                if ($isCorrect) {
                    $correctCount++;
                }

                $attempt->answers()->create([
                    'quiz_question_id' => $questionId,
                    'selected_answer_id' => $answerId,
                    'is_correct' => $isCorrect,
                ]);
            }

            // Let the model handle score and percentage calculation
            $attempt->markAsCompleted();
            $attempt->refresh();

            // Re-evaluasi kelulusan untuk auto-generate cert 
            $enrollment->updateProgress();

            DB::commit();

            return $this->success([
                'attempt_id' => $attempt->id,
                'score' => (int) $attempt->percentage, // The frontend expects a 0-100 score
                'is_passed' => $attempt->is_passed,
                'correct_answers' => $attempt->score, // Model stores correct answers in `score`
                'total_questions' => $attempt->total_questions,
                'passing_score' => \App\Models\QuizAttempt::PASSING_PERCENTAGE,
            ], 'Kuis berhasil disubmit');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Terjadi kesalahan saat menyimpan jawaban kuis: ' . $e->getMessage(), 500);
        }
    }

    protected function resolveEnrollment(Request $request, string $courseSlug): ?CourseEnrollment
    {
        return CourseEnrollment::query()
            ->where('user_id', $request->user()->id)
            ->whereHas('course', function ($query) use ($courseSlug) {
                $query->where('slug', $courseSlug)->where('is_published', true);
            })
            ->first();
    }
}
