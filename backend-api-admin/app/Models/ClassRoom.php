<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'school_id',
        'homeroom_teacher_id',
        'name',
        'level',
        'capacity',
        'academic_year',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'homeroom_teacher_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'class_id');
    }

    // Scopes
    public function scopeByAcademicYear($query, string $year)
    {
        return $query->where('academic_year', $year);
    }

    public function scopeByLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    // Helper Methods
    public function getCurrentStudentCount(): int
    {
        return $this->students()->where('status', 'active')->count();
    }

    public function hasCapacity(): bool
    {
        if (!$this->capacity) {
            return true; // No capacity limit
        }

        return $this->getCurrentStudentCount() < $this->capacity;
    }

    public function getHomeroomTeacherNameAttribute(): ?string
    {
        return $this->homeroomTeacher?->user?->name;
    }
}
