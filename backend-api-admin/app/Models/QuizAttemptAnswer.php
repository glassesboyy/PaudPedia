<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttemptAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_attempt_id',
        'quiz_question_id',
        'selected_answer_id',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    // Relationships
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }

    public function selectedAnswer(): BelongsTo
    {
        return $this->belongsTo(QuizAnswer::class, 'selected_answer_id');
    }

    // Helper Methods

    /**
     * Check and set if the selected answer is correct
     */
    public function evaluateAnswer(): self
    {
        if ($this->selected_answer_id) {
            $correctAnswer = $this->question->getCorrectAnswer();
            $this->is_correct = $correctAnswer && $correctAnswer->id === $this->selected_answer_id;
        } else {
            $this->is_correct = false;
        }
        
        return $this;
    }

    /**
     * Set the selected answer and evaluate correctness
     */
    public function setAnswer(int $answerId): self
    {
        $this->selected_answer_id = $answerId;
        return $this->evaluateAnswer();
    }

    /**
     * Check if user answered this question
     */
    public function hasAnswered(): bool
    {
        return $this->selected_answer_id !== null;
    }

    /**
     * Get the correct answer for this question
     */
    public function getCorrectAnswerAttribute(): ?QuizAnswer
    {
        return $this->question->getCorrectAnswer();
    }
}
