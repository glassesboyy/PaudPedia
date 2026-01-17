<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    use HasFactory;

    protected $table = 'lesson_progress';

    protected $fillable = [
        'enrollment_id',
        'lesson_id',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(CourseEnrollment::class, 'enrollment_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    // Helper Methods
    public function markAsCompleted(): void
    {
        if (!$this->is_completed) {
            $this->is_completed = true;
            $this->completed_at = now();
            $this->save();

            // Update enrollment progress
            $this->enrollment->updateProgress();
        }
    }

    public function markAsIncomplete(): void
    {
        if ($this->is_completed) {
            $this->is_completed = false;
            $this->completed_at = null;
            $this->save();

            // Update enrollment progress
            $this->enrollment->updateProgress();
        }
    }
}
