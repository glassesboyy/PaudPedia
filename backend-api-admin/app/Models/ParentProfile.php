<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'user_id',
        'email',
        'father_name',
        'mother_name',
        'phone',
        'father_occupation',
        'mother_occupation',
        'address',
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

    public function children(): HasMany
    {
        return $this->hasMany(Student::class, 'parent_profile_id');
    }

    // Scopes
    public function scopeBySchool($query, int $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    // Helper Methods
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    public function getActiveChildrenCount(): int
    {
        return $this->children()->where('status', 'active')->count();
    }

    public function getPrimaryParentNameAttribute(): string
    {
        return $this->father_name ?? $this->mother_name ?? 'Unknown';
    }
}
