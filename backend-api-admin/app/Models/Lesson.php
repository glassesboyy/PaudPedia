<?php

namespace App\Models;

use App\Enums\ContentType;
use App\Models\LessonProgress;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Clean up old PDF file when pdf_file changes or lesson is deleted
        // Using 'local' disk because PDF files are stored privately for paid course content
        static::updating(function (Lesson $lesson) {
            if ($lesson->isDirty('pdf_file')) {
                $oldPdfFile = $lesson->getOriginal('pdf_file');
                if ($oldPdfFile && Storage::disk('local')->exists($oldPdfFile)) {
                    Storage::disk('local')->delete($oldPdfFile);
                }
            }
        });

        static::deleting(function (Lesson $lesson) {
            if ($lesson->pdf_file && Storage::disk('local')->exists($lesson->pdf_file)) {
                Storage::disk('local')->delete($lesson->pdf_file);
            }
        });
    }

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
