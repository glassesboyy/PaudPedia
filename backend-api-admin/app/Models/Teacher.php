<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'school_id',
        'nip',
        'specialization',
        'bio',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function homeroomClasses(): HasMany
    {
        return $this->hasMany(ClassRoom::class, 'homeroom_teacher_id');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    // Helper Methods
    public function getFullNameAttribute(): string
    {
        return $this->user->name ?? 'Unknown';
    }

    public function getTotalHomeroomClassesAttribute(): int
    {
        return $this->homeroomClasses()->count();
    }
}
