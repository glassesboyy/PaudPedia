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
        'content_url',
        'duration_minutes',
        'order',
        'is_preview',
    ];

    protected $casts = [
        'content_type' => ContentType::class,
        'duration_minutes' => 'integer',
        'order' => 'integer',
        'is_preview' => 'boolean',
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
    public function scopePreview($query)
    {
        return $query->where('is_preview', true);
    }

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

    public function isQuiz(): bool
    {
        return $this->content_type === ContentType::QUIZ;
    }

    public function canBePreview(): bool
    {
        return $this->content_type->canBePreview();
    }
}
