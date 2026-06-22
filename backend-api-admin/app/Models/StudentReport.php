<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\Semester;

class StudentReport extends Model
{
    protected $fillable = [
        'school_id',
        'student_id',
        'class_id',
        'teacher_id',
        'semester',
        'academic_year',
        'introduction_notes',
        'closing_notes',
        'recommendation',
    ];

    protected $casts = [
        'semester' => Semester::class,
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(StudentReportDetail::class, 'student_report_id');
    }
}
