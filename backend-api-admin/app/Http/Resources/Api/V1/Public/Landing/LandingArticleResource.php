<?php

namespace App\Http\Resources\Api\V1\Public\Landing;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Landing-specific Article Resource.
 *
 * Optimized payload for the landing page — only includes fields
 * actually rendered by the ArticleCard component.
 *
 * Excluded vs full ArticleResource: view_count
 * Category: only id + name (slug not used on card)
 */
class LandingArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt ?? $this->getExcerpt(),
            'featured_image_url' => $this->featured_image_url ? asset('storage/' . $this->featured_image_url) : null,
            'tags' => is_array($this->tags) ? $this->tags : (is_string($this->tags) && !empty($this->tags) ? array_map('trim', explode(',', $this->tags)) : []),
            'reading_time' => $this->reading_time ?? $this->computeReadingTime(),
            'is_featured' => $this->is_featured,
            'author' => $this->whenLoaded('author', function () {
                return [
                    'id' => $this->author?->id,
                    'name' => $this->author?->name ?? 'Unknown',
                ];
            }),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category?->id,
                    'name' => $this->category?->name,
                ];
            }),
            'published_at' => $this->published_at?->toIso8601String(),
            'published_date' => $this->formatted_published_date,
        ];
    }

    protected function getExcerpt(): ?string
    {
        if (!isset($this->attributes['content']) || !$this->content) {
            return null;
        }

        $plainText = strip_tags($this->content);
        return strlen($plainText) > 200 ? substr($plainText, 0, 200) . '...' : $plainText;
    }

    protected function computeReadingTime(): int
    {
        if (!isset($this->attributes['content']) || !$this->content) {
            return 1;
        }

        $plainText = strip_tags($this->content);
        $wordCount = str_word_count($plainText);
        $readingTime = ceil($wordCount / 200);

        return max(1, $readingTime);
    }
}
