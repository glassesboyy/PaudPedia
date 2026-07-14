<?php

namespace App\Models;

use App\Enums\SubscriptionPlan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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
        'subscription_started_at',
        'subscription_ended_at',
        'is_active',
    ];

    protected $casts = [
        'subscription_plan' => SubscriptionPlan::class,
        'subscription_started_at' => 'datetime',
        'subscription_ended_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'total_students',
        'total_teachers',
        'total_classes',
        'total_parents',
        'headmaster_name',
        'headmaster_email',
        'headmaster_phone',
    ];

    // Relationships
    public function members(): HasMany
    {
        return $this->hasMany(SchoolMember::class)
            ->where('school_id', $this->id); // 🔒 DATA ISOLATION
    }

    public function headmaster(): HasMany
    {
        return $this->hasMany(SchoolMember::class)
            ->where('school_id', $this->id) // 🔒 DATA ISOLATION
            ->where('role_type', \App\Enums\RoleType::HEADMASTER)
            ->with('user'); // Eager load user data
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class)
            ->where('school_id', $this->id) // 🔒 DATA ISOLATION
            ->with('user'); // Eager load user data
    }

    public function classes(): HasMany
    {
        return $this->hasMany(ClassRoom::class)
            ->where('school_id', $this->id); // 🔒 DATA ISOLATION
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class)
            ->where('school_id', $this->id); // 🔒 DATA ISOLATION
    }

    public function parentProfiles(): HasMany
    {
        return $this->hasMany(ParentProfile::class)
            ->where('school_id', $this->id); // 🔒 DATA ISOLATION
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class)
            ->where('school_id', $this->id); // 🔒 DATA ISOLATION
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class)
            ->where('school_id', $this->id); // 🔒 DATA ISOLATION
    }

    public function finances(): HasMany
    {
        return $this->hasMany(Finance::class)
            ->where('school_id', $this->id); // 🔒 DATA ISOLATION
    }

    // Scopes
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopePro(Builder $query)
    {
        return $query->where('subscription_plan', SubscriptionPlan::PRO);
    }

    public function scopeFree(Builder $query)
    {
        return $query->where('subscription_plan', SubscriptionPlan::FREE);
    }

    // Accessors
    protected function totalStudents(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->students()->count(),
        );
    }

    protected function totalTeachers(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->teachers()->count(),
        );
    }

    protected function totalClasses(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->classes()->count(),
        );
    }

    protected function totalParents(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->parentProfiles()->count(),
        );
    }

    protected function headmasterName(): Attribute
    {
        return Attribute::make(
            // 🔒 Ambil hanya headmaster dari school_id = $this->id
            get: function () {
                $headmaster = $this->headmaster()
                    ->where('school_id', $this->id) // Double check data isolation
                    ->with('user')
                    ->first();
                
                return $headmaster?->user?->name ?? '-';
            },
        );
    }

    protected function headmasterEmail(): Attribute
    {
        return Attribute::make(
            // 🔒 Ambil hanya headmaster dari school_id = $this->id
            get: function () {
                $headmaster = $this->headmaster()
                    ->where('school_id', $this->id) // Double check data isolation
                    ->with('user')
                    ->first();
                
                return $headmaster?->user?->email ?? '-';
            },
        );
    }

    protected function headmasterPhone(): Attribute
    {
        return Attribute::make(
            // 🔒 Ambil hanya headmaster dari school_id = $this->id
            get: function () {
                $headmaster = $this->headmaster()
                    ->where('school_id', $this->id) // Double check data isolation
                    ->with('user')
                    ->first();
                
                return $headmaster?->user?->phone ?? '-';
            },
        );
    }

    protected function studentLimit(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->subscription_plan->studentLimit() ?? 9999,
        );
    }

    protected function teacherLimit(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->subscription_plan->teacherLimit() ?? 9999,
        );
    }

    // Helper Methods
    public function getLogoUrl(): string
    {
        if ($this->logo_url && Storage::disk('public')->exists($this->logo_url)) {
            return asset('storage/' . $this->logo_url);
        }

        // Return default icon if not found
        return asset('images/default-school.png');
    }

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
        
        $activeTeachersCount = \App\Models\SchoolMember::where('school_id', $this->id)
            ->where('role_type', \App\Enums\RoleType::TEACHER)
            ->where('is_active', true)
            ->count();
            
        return $activeTeachersCount < $this->teacher_limit;
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

        $activeTeachers = \App\Models\SchoolMember::where('school_id', $this->id)
            ->where('role_type', \App\Enums\RoleType::TEACHER)
            ->where('is_active', true)
            ->count();

        return max(0, $this->teacher_limit - $activeTeachers);
    }
}
