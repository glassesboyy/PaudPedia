<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Clean up old photo when photo_url changes or mentor is deleted
        static::updating(function (Mentor $mentor) {
            if ($mentor->isDirty('photo_url')) {
                $oldPhoto = $mentor->getOriginal('photo_url');
                if ($oldPhoto && Storage::disk('public')->exists($oldPhoto)) {
                    Storage::disk('public')->delete($oldPhoto);
                }
            }
        });

        static::deleting(function (Mentor $mentor) {
            if ($mentor->photo_url && Storage::disk('public')->exists($mentor->photo_url)) {
                Storage::disk('public')->delete($mentor->photo_url);
            }
        });
    }

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
    public function scopeActive(Builder $query)
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
