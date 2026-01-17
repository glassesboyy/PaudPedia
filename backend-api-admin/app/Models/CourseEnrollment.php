<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'enrolled_at',
        'progress_percentage',
        'completed_at',
        'certificate_url',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'progress_percentage' => 'integer',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class, 'enrollment_id');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    public function scopeInProgress($query)
    {
        return $query->whereNull('completed_at');
    }

    // Helper Methods
    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function updateProgress(): void
    {
        $totalLessons = $this->course->total_lessons ?? 0;
        
        if ($totalLessons === 0) {
            $this->progress_percentage = 0;
            $this->save();
            return;
        }

        $completedLessons = $this->lessonProgress()->where('is_completed', true)->count();
        $percentage = ($completedLessons / $totalLessons) * 100;

        $this->progress_percentage = (int) round($percentage);

        // Auto-complete if 100%
        if ($this->progress_percentage >= 100 && !$this->isCompleted()) {
            $this->completed_at = now();
            // TODO: Generate certificate
        }

        $this->save();
    }

    public function getCompletedLessonsCountAttribute(): int
    {
        return $this->lessonProgress()->where('is_completed', true)->count();
    }

    public function getTotalLessonsCountAttribute(): int
    {
        return $this->course->total_lessons ?? 0;
    }
}
