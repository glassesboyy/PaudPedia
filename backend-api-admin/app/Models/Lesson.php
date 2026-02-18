<?php

namespace App\Models;

use App\Enums\ContentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'content_type',
        'video_url',
        'pdf_file',
        'text_content',
        'duration_minutes',
        'order',
    ];

    protected $casts = [
        'content_type' => ContentType::class,
        'duration_minutes' => 'integer',
        'order' => 'integer',
    ];

    // Relationships
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    // Scopes
    public function scopeByType($query, ContentType $type)
    {
        return $query->where('content_type', $type);
    }

    // Helper Methods
    public function isVideo(): bool
    {
        return $this->content_type === ContentType::VIDEO;
    }

    public function isPdf(): bool
    {
        return $this->content_type === ContentType::PDF;
    }

    public function isText(): bool
    {
        return $this->content_type === ContentType::TEXT;
    }

    /**
     * Get the content based on content type
     */
    public function getContent(): ?string
    {
        return match($this->content_type) {
            ContentType::VIDEO => $this->video_url,
            ContentType::PDF => $this->pdf_file,
            ContentType::TEXT => $this->text_content,
            default => null,
        };
    }
}
