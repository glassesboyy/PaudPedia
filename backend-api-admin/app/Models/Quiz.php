<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'description',
    ];

    protected $casts = [
        //
    ];

    // Relationships
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    // Helper Methods
    public function getTotalQuestionsAttribute(): int
    {
        return $this->questions()->count();
    }

    /**
     * Check if quiz has minimum required questions
     */
    public function hasMinimumQuestions(): bool
    {
        return $this->questions()->count() >= 1;
    }

    /**
     * Check if each question has minimum required answers
     */
    public function hasValidQuestions(): bool
    {
        foreach ($this->questions as $question) {
            if ($question->answers()->count() < 2) {
                return false;
            }
            if (!$question->hasCorrectAnswer()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get user's best attempt for this quiz
     */
    public function getUserBestAttempt(int $userId): ?QuizAttempt
    {
        return QuizAttempt::getBestAttempt($this->id, $userId);
    }

    /**
     * Get user's latest attempt for this quiz
     */
    public function getUserLatestAttempt(int $userId): ?QuizAttempt
    {
        return QuizAttempt::getLatestAttempt($this->id, $userId);
    }

    /**
     * Check if user has passed this quiz
     */
    public function hasUserPassed(int $userId): bool
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->where('is_passed', true)
            ->exists();
    }

    /**
     * Get attempt count for a user
     */
    public function getUserAttemptCount(int $userId): int
    {
        return QuizAttempt::countAttempts($this->id, $userId);
    }

    /**
     * Start a new attempt for a user
     */
    public function startAttempt(int $userId, int $enrollmentId): QuizAttempt
    {
        return $this->attempts()->create([
            'user_id' => $userId,
            'enrollment_id' => $enrollmentId,
            'total_questions' => $this->questions()->count(),
            'started_at' => now(),
        ]);
    }
}
