<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeHighRated($query)
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
