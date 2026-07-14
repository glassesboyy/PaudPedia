<?php

namespace App\Models;

use App\Enums\AssessmentScale;
use App\Enums\Semester;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'indicator_id',
        'assessment_month',
        'scale',
        'semester',
        'academic_year',
        'notes',
        'assessed_at',
    ];

    protected $casts = [
        'assessed_at' => 'datetime',
        'scale' => AssessmentScale::class,
        'semester' => Semester::class,
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(DevelopmentIndicator::class, 'indicator_id');
    }

    // Indirect relationships via student
    public function school()
    {
        return $this->student->school();
    }

    public function class()
    {
        return $this->student->class();
    }

    // Scopes
    public function scopeBySemester(Builder $query, Semester $semester)
    {
        return $query->where('semester', $semester);
    }

    public function scopeByMonth(Builder $query, string $month)
    {
        return $query->where('assessment_month', $month);
    }

    public function scopeByScale(Builder $query, AssessmentScale $scale)
    {
        return $query->where('scale', $scale);
    }

    public function scopePassing(Builder $query)
    {
        return $query->whereIn('scale', [AssessmentScale::BSH, AssessmentScale::BSB]);
    }

    // Helper Methods
    public function isPassing(): bool
    {
        return $this->scale->isPassing();
    }

    public function getNumericScore(): int
    {
        return $this->scale->numericValue();
    }
}
