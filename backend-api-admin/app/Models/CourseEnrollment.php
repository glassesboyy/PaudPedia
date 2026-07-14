<?php

namespace App\Models;

use App\Services\Lms\CertificateGeneratorService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Log;

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
    public function scopeCompleted(Builder $query)
    {
        return $query->whereNotNull('completed_at');
    }

    public function scopeInProgress(Builder $query)
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

        // Required Quiz Validation logic
        $hasQuizzes = $this->course->modules()->has('quiz')->exists();
        $allQuizzesPassed = true;

        if ($hasQuizzes) {
            $courseQuizIds = Quiz::whereHas('module', function ($query) {
                $query->where('course_id', $this->course_id);
            })->pluck('id');

            $passedQuizCount = QuizAttempt::where('user_id', $this->user_id)
                ->whereIn('quiz_id', $courseQuizIds)
                ->where('is_passed', true)
                ->count(); // Using count instead of distinct since we just need to verify they passed every quiz

            // Since it's possible the user passed the same quiz multiple times, we need a better check
            $passedUniqueQuizzes = QuizAttempt::where('user_id', $this->user_id)
                ->whereIn('quiz_id', $courseQuizIds)
                ->where('is_passed', true)
                ->select('quiz_id')
                ->distinct()
                ->count('quiz_id');

            if ($passedUniqueQuizzes < $courseQuizIds->count()) {
                $allQuizzesPassed = false;
            }
        }

        // Auto-complete if 100% and passed required quizzes
        if ($this->progress_percentage >= 100 && $allQuizzesPassed && !$this->isCompleted()) {
            $this->completed_at = now();
        }

        $this->save();

        if ($this->progress_percentage >= 100 && $allQuizzesPassed && empty($this->certificate_url)) {
            try {
                $this->loadMissing(['user', 'course']);

                $path = app(CertificateGeneratorService::class)->generateForEnrollment($this);

                if ($path) {
                    $this->certificate_url = $path;
                    $this->save();
                }
            } catch (\Throwable $e) {
                Log::error('Failed generating certificate for enrollment', [
                    'enrollment_id' => $this->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
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
