<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question',
    ];

    // Relationships
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }

    // Helper Methods
    public function getCorrectAnswer(): ?QuizAnswer
    {
        return $this->answers()->where('is_correct', true)->first();
    }

    public function hasCorrectAnswer(): bool
    {
        return $this->answers()->where('is_correct', true)->exists();
    }

    public function getTotalAnswersAttribute(): int
    {
        return $this->answers()->count();
    }
}
