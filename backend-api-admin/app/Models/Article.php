<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'author_id',
        'title',
        'slug',
        'content',
        'excerpt',
        'featured_image_url',
        'tags',
        'view_count',
        'reading_time',
        'is_featured',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'view_count' => 'integer',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-compute reading_time whenever content changes
        static::saving(function (Article $article) {
            if ($article->isDirty('content') && $article->content) {
                $plainText = strip_tags($article->content);
                $wordCount = str_word_count($plainText);
                $article->reading_time = max(1, (int) ceil($wordCount / 200));
            }
        });

        // Clean up old featured image when featured_image_url changes or article is deleted
        static::updating(function (Article $article) {
            if ($article->isDirty('featured_image_url')) {
                $oldImage = $article->getOriginal('featured_image_url');
                if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        });

        static::deleting(function (Article $article) {
            if ($article->featured_image_url && Storage::disk('public')->exists($article->featured_image_url)) {
                Storage::disk('public')->delete($article->featured_image_url);
            }
        });
    }

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
    public function scopePublished(Builder $query)
    {
        return $query->where('is_published', true)
                    ->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByTag(Builder $query, string $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function scopePopular(Builder $query)
    {
        return $query->orderBy('view_count', 'desc');
    }

    public function scopeRecent(Builder $query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    /**
     * Select only the columns needed for list/card views (excludes heavy content column).
     */
    public function scopeListColumns(Builder $query)
    {
        return $query->select([
            'id', 'category_id', 'author_id', 'title', 'slug',
            'excerpt', 'featured_image_url', 'tags', 'view_count',
            'reading_time', 'is_featured', 'is_published', 'published_at',
            'created_at', 'updated_at', 'deleted_at',
        ]);
    }

    // Helper Methods
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
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
