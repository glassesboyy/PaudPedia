<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'author_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image_url',
        'tags',
        'reading_time_minutes',
        'view_count',
        'is_featured',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'reading_time_minutes' => 'integer',
        'view_count' => 'integer',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByTag($query, string $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('view_count', 'desc');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    // Helper Methods
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function calculateReadingTime(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / 200); // Average reading speed: 200 words/minute
        
        return max(1, $minutes);
    }

    public function getFormattedPublishedDateAttribute(): string
    {
        return $this->published_at?->format('d M Y') ?? '-';
    }

    public function getAuthorNameAttribute(): string
    {
        return $this->author?->name ?? 'Unknown';
    }
}
