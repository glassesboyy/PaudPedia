<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mentor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'bio',
        'photo_url',
        'expertise',
        'social_media',
        'is_active',
    ];

    protected $casts = [
        'social_media' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function webinars(): HasMany
    {
        return $this->hasMany(Webinar::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper Methods
    public function getTotalWebinarsAttribute(): int
    {
        return $this->webinars()->count();
    }

    public function getTotalCoursesAttribute(): int
    {
        return $this->courses()->count();
    }

    public function getFullTitleAttribute(): string
    {
        return $this->name . ($this->title ? ', ' . $this->title : '');
    }
}
