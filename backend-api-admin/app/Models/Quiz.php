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
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
}
