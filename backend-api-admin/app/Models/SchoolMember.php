<?php

namespace App\Models;

use App\Enums\RoleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'user_id',
        'role',
        'is_active',
        'joined_at',
    ];

    protected $casts = [
        'role' => RoleType::class,
        'is_active' => 'boolean',
        'joined_at' => 'datetime',
    ];

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, RoleType $role)
    {
        return $query->where('role', $role);
    }

    public function scopeHeadmasters($query)
    {
        return $query->where('role', RoleType::HEADMASTER);
    }

    public function scopeTeachers($query)
    {
        return $query->where('role', RoleType::TEACHER);
    }

    public function scopeParents($query)
    {
        return $query->where('role', RoleType::PARENT);
    }

    // Helper Methods
    public function isHeadmaster(): bool
    {
        return $this->role === RoleType::HEADMASTER;
    }

    public function isTeacher(): bool
    {
        return $this->role === RoleType::TEACHER;
    }

    public function isParent(): bool
    {
        return $this->role === RoleType::PARENT;
    }
}
