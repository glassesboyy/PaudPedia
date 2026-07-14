<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'enrollment_id',
        'score',
        'total_questions',
        'percentage',
        'is_passed',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'total_questions' => 'integer',
        'percentage' => 'decimal:2',
        'is_passed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Default passing percentage (can be configured per quiz if needed)
    const PASSING_PERCENTAGE = 70.00;

    // Relationships
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(CourseEnrollment::class, 'enrollment_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class);
    }

    // Scopes
    public function scopePassed(Builder $query)
    {
        return $query->where('is_passed', true);
    }

    public function scopeFailed(Builder $query)
    {
        return $query->where('is_passed', false);
    }

    public function scopeCompleted(Builder $query)
    {
        return $query->whereNotNull('completed_at');
    }

    public function scopeByUser(Builder $query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helper Methods
    
    /**
     * Calculate and update the score based on answers
     */
    public function calculateScore(): self
    {
        $correctAnswers = $this->answers()->where('is_correct', true)->count();
        $totalQuestions = $this->quiz->questions()->count();
        
        $this->score = $correctAnswers;
        $this->total_questions = $totalQuestions;
        $this->percentage = $totalQuestions > 0 
            ? round(($correctAnswers / $totalQuestions) * 100, 2) 
            : 0;
        $this->is_passed = $this->percentage >= self::PASSING_PERCENTAGE;
        
        return $this;
    }

    /**
     * Mark the attempt as completed
     */
    public function markAsCompleted(): self
    {
        $this->completed_at = now();
        $this->calculateScore();
        $this->save();
        
        return $this;
    }

    /**
     * Check if attempt is completed
     */
    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    /**
     * Get formatted score display
     */
    public function getScoreDisplayAttribute(): string
    {
        return "{$this->score}/{$this->total_questions}";
    }

    /**
     * Get formatted percentage display
     */
    public function getPercentageDisplayAttribute(): string
    {
        return number_format($this->percentage, 1) . '%';
    }

    /**
     * Get duration of the attempt in minutes
     */
    public function getDurationMinutesAttribute(): ?int
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }
        
        return $this->started_at->diffInMinutes($this->completed_at);
    }

    /**
     * Get the best attempt for a user on a quiz
     */
    public static function getBestAttempt(int $quizId, int $userId): ?self
    {
        return static::where('quiz_id', $quizId)
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->orderByDesc('percentage')
            ->first();
    }

    /**
     * Get the latest attempt for a user on a quiz
     */
    public static function getLatestAttempt(int $quizId, int $userId): ?self
    {
        return static::where('quiz_id', $quizId)
            ->where('user_id', $userId)
            ->latest()
            ->first();
    }

    /**
     * Count total attempts by user for a quiz
     */
    public static function countAttempts(int $quizId, int $userId): int
    {
        return static::where('quiz_id', $quizId)
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->count();
    }
}
