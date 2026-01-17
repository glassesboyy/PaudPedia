<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'school_id',
        'student_id',
        'class_id',
        'teacher_id',
        'date',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'status' => AttendanceStatus::class,
    ];

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    // Scopes
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeByMonth($query, int $month, ?int $year = null)
    {
        $query->whereMonth('date', $month);
        
        if ($year) {
            $query->whereYear('date', $year);
        }
        
        return $query;
    }

    public function scopeByStatus($query, AttendanceStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopePresent($query)
    {
        return $query->where('status', AttendanceStatus::PRESENT);
    }

    // Helper Methods
    public function isPresent(): bool
    {
        return $this->status === AttendanceStatus::PRESENT;
    }

    public function requiresNotes(): bool
    {
        return $this->status->requiresNotes();
    }
}
