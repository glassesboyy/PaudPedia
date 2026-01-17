<?php

namespace App\Models;

use App\Enums\CourseLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'category_id',
        'title',
        'slug',
        'description',
        'thumbnail_url',
        'price',
        'original_price',
        'level',
        'duration_hours',
        'is_published',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'level' => CourseLevel::class,
        'duration_hours' => 'integer',
        'is_published' => 'boolean',
    ];

    // Relationships
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function orderItems(): MorphMany
    {
        return $this->morphMany(OrderItem::class, 'item', 'item_type', 'item_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByLevel($query, CourseLevel $level)
    {
        return $query->where('level', $level);
    }

    public function scopeBeginner($query)
    {
        return $query->where('level', CourseLevel::BEGINNER);
    }

    // Helper Methods
    public function hasDiscount(): bool
    {
        return $this->original_price && $this->original_price > $this->price;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->hasDiscount()) {
            return null;
        }

        return (int) round((($this->original_price - $this->price) / $this->original_price) * 100);
    }

    public function getTotalLessonsAttribute(): int
    {
        return $this->modules->sum(function($module) {
            return $module->lessons()->count();
        });
    }

    public function getTotalEnrollmentsAttribute(): int
    {
        return $this->enrollments()->count();
    }

    public function getTotalCompletionsAttribute(): int
    {
        return $this->enrollments()->whereNotNull('completed_at')->count();
    }

    public function getCompletionRateAttribute(): float
    {
        $total = $this->getTotalEnrollmentsAttribute();
        if ($total === 0) {
            return 0;
        }

        return round(($this->getTotalCompletionsAttribute() / $total) * 100, 2);
    }
}
