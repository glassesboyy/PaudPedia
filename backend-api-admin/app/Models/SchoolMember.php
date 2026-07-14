<?php

namespace App\Models;

use App\Enums\RoleType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_id',
        'user_id',
        'role_type',
        'is_active',
        'joined_at',
    ];

    protected $casts = [
        'role_type' => RoleType::class,
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
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole(Builder $query, RoleType $role)
    {
        return $query->where('role_type', $role);
    }

    public function scopeHeadmasters(Builder $query)
    {
        return $query->where('role_type', RoleType::HEADMASTER);
    }

    public function scopeOperators(Builder $query)
    {
        return $query->where('role_type', RoleType::OPERATOR);
    }

    public function scopeTeachers(Builder $query)
    {
        return $query->where('role_type', RoleType::TEACHER);
    }

    public function scopeParents(Builder $query)
    {
        return $query->where('role_type', RoleType::PARENT);
    }

    // Helper Methods
    public function isHeadmaster(): bool
    {
        return $this->role_type === RoleType::HEADMASTER;
    }

    public function isOperator(): bool
    {
        return $this->role_type === RoleType::OPERATOR;
    }

    public function isTeacher(): bool
    {
        return $this->role_type === RoleType::TEACHER;
    }

    public function isParent(): bool
    {
        return $this->role_type === RoleType::PARENT;
    }

    /**
     * Check if this member is a "manager" (headmaster or operator).
     * Used for authorization on endpoints accessible by both roles.
     */
    public function isManager(): bool
    {
        return in_array($this->role_type, [RoleType::HEADMASTER, RoleType::OPERATOR]);
    }
}
