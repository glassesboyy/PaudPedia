<?php

namespace App\Models;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'gender',
        'date_of_birth',
        'address',
        'avatar_url',
        'is_active',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'gender' => Gender::class,
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function schoolMemberships(): HasMany
    {
        return $this->hasMany(SchoolMember::class);
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentProfile::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    // Helper Methods
    public function isTeacher(): bool
    {
        return $this->teacher !== null;
    }

    public function isParent(): bool
    {
        return $this->parentProfile !== null;
    }

    public function getSchools()
    {
        return $this->schoolMemberships()
                    ->with('school')
                    ->get()
                    ->pluck('school');
    }

    public function hasSchoolRole(int $schoolId, string $roleName): bool
    {
        return $this->schoolMemberships()
                    ->where('school_id', $schoolId)
                    ->whereHas('role', function($query) use ($roleName) {
                        $query->where('name', $roleName);
                    })
                    ->exists();
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }
}

