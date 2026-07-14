<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'title',
        'content',
        'rating',
        'photo_url',
        'is_featured',
        'is_approved',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_featured' => 'boolean',
        'is_approved' => 'boolean',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Clean up old photo when photo_url changes or testimonial is deleted
        static::updating(function (Testimonial $testimonial) {
            if ($testimonial->isDirty('photo_url')) {
                $oldPhoto = $testimonial->getOriginal('photo_url');
                if ($oldPhoto && Storage::disk('public')->exists($oldPhoto)) {
                    Storage::disk('public')->delete($oldPhoto);
                }
            }
        });

        static::deleting(function (Testimonial $testimonial) {
            if ($testimonial->photo_url && Storage::disk('public')->exists($testimonial->photo_url)) {
                Storage::disk('public')->delete($testimonial->photo_url);
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeApproved(Builder $query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeFeatured(Builder $query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeHighRated(Builder $query)
    {
        return $query->where('rating', '>=', 4);
    }

    // Helper Methods
    public function getDisplayNameAttribute(): string
    {
        return $this->user?->name ?? $this->name;
    }

    public function getStarRatingAttribute(): string
    {
        return str_repeat('⭐', $this->rating);
    }
}
