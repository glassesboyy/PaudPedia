<?php

namespace App\Models;

use App\Enums\SubscriptionPlan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'npsn',
        'address',
        'phone',
        'email',
        'logo_url',
        'subscription_plan',
        'student_limit',
        'teacher_limit',
        'subscription_expires_at',
        'is_active',
    ];

    protected $casts = [
        'subscription_plan' => SubscriptionPlan::class,
        'student_limit' => 'integer',
        'teacher_limit' => 'integer',
        'subscription_expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function members(): HasMany
    {
        return $this->hasMany(SchoolMember::class);
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(ClassRoom::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function parentProfiles(): HasMany
    {
        return $this->hasMany(ParentProfile::class);
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
        return $query->where('is_active', true);
    }

    public function scopePro($query)
    {
        return $query->where('subscription_plan', SubscriptionPlan::PRO);
    }

    public function scopeFree($query)
    {
        return $query->where('subscription_plan', SubscriptionPlan::FREE);
    }

    // Helper Methods
    public function isPro(): bool
    {
        return $this->subscription_plan === SubscriptionPlan::PRO;
    }

    public function isFree(): bool
    {
        return $this->subscription_plan === SubscriptionPlan::FREE;
    }

    public function canAddStudent(): bool
    {
        if ($this->isPro()) {
            return true;
        }
        
        return $this->students()->where('status', 'active')->count() < $this->student_limit;
    }

    public function canAddTeacher(): bool
    {
        if ($this->isPro()) {
            return true;
        }
        
        return $this->teachers()->count() < $this->teacher_limit;
    }

    public function hasFeature(string $feature): bool
    {
        $proFeatures = ['pdf_report', 'finance_management'];
        
        if (!in_array($feature, $proFeatures)) {
            return true; // Free feature
        }
        
        return $this->isPro();
    }

    public function getRemainingStudentSlots(): int
    {
        if ($this->isPro()) {
            return PHP_INT_MAX; // Unlimited
        }

        $activeStudents = $this->students()->where('status', 'active')->count();
        return max(0, $this->student_limit - $activeStudents);
    }

    public function getRemainingTeacherSlots(): int
    {
        if ($this->isPro()) {
            return PHP_INT_MAX; // Unlimited
        }

        $activeTeachers = $this->teachers()->count();
        return max(0, $this->teacher_limit - $activeTeachers);
    }
}
