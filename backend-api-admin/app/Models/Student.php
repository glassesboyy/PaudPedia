<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_id',
        'parent_profile_id',
        'name',
        'nisn',
        'birth_date',
        'gender',
        'address',
        'photo_url',
        'enrollment_date',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'enrollment_date' => 'date',
        'gender' => Gender::class,
        'status' => StudentStatus::class,
    ];

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentProfile::class, 'parent_profile_id');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function finances(): HasMany
    {
        return $this->hasMany(Finance::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', StudentStatus::ACTIVE);
    }

    public function scopeBySchool($query, int $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByClass($query, int $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeByGender($query, Gender $gender)
    {
        return $query->where('gender', $gender);
    }

    // Helper Methods
    public function getAge(): int
    {
        return $this->birth_date->age;
    }

    public function getAgeInMonths(): int
    {
        return $this->birth_date->diffInMonths(Carbon::now());
    }

    public function getAttendancePercentage(?string $month = null, ?string $year = null): float
    {
        $query = $this->attendance();
        
        if ($month && $year) {
            $query->whereMonth('date', $month)->whereYear('date', $year);
        } elseif ($month) {
            $query->whereMonth('date', $month);
        } elseif ($year) {
            $query->whereYear('date', $year);
        }
        
        $total = $query->count();
        if ($total === 0) {
            return 0;
        }
        
        $present = $query->where('status', 'present')->count();
        
        return round(($present / $total) * 100, 2);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->nickname ?? $this->full_name;
    }

    public function isActive(): bool
    {
        return $this->status === StudentStatus::ACTIVE;
    }
}
