<?php

namespace App\Models;

use App\Enums\CategoryType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
    ];

    protected $casts = [
        'type' => CategoryType::class,
    ];

    // Relationships
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    // Scopes
    public function scopeByType(Builder $query, CategoryType $type)
    {
        return $query->where('type', $type);
    }

    public function scopeCourseCategories(Builder $query)
    {
        return $query->where('type', CategoryType::COURSE);
    }

    public function scopeProductCategories(Builder $query)
    {
        return $query->where('type', CategoryType::PRODUCT);
    }

    public function scopeArticleCategories(Builder $query)
    {
        return $query->where('type', CategoryType::ARTICLE);
    }

    // Helper Methods
    public function getItemsCountAttribute(): int
    {
        return match($this->type) {
            CategoryType::COURSE => $this->courses()->count(),
            CategoryType::PRODUCT => $this->products()->count(),
            CategoryType::ARTICLE => $this->articles()->count(),
            default => 0,
        };
    }
}
