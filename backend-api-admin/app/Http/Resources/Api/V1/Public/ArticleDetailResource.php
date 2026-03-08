<?php

namespace App\Http\Resources\Api\V1\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'featured_image_url' => $this->featured_image_url ? asset('storage/' . $this->featured_image_url) : null,
            'tags' => is_array($this->tags) ? $this->tags : (is_string($this->tags) && !empty($this->tags) ? array_map('trim', explode(',', $this->tags)) : []),
            'view_count' => $this->view_count ?? 0,
            'reading_time' => $this->getReadingTime(),
            'is_featured' => $this->is_featured,
            'author' => $this->whenLoaded('author', function () {
                return [
                    'id' => $this->author?->id,
                    'name' => $this->author?->name ?? 'Unknown',
                    'email' => $this->author?->email,
                ];
            }),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category?->id,
                    'name' => $this->category?->name,
                    'slug' => $this->category?->slug,
                    'description' => $this->category?->description,
                ];
            }),
            'published_at' => $this->published_at?->toIso8601String(),
            'published_date' => $this->formatted_published_date,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            // SEO meta
            'meta' => [
                'title' => $this->seo_title ?? $this->title,
                'description' => $this->seo_description ?? $this->excerpt,
                'keywords' => $this->seo_keywords ?? $this->tags,
            ],
        ];
    }

    /**
     * Calculate reading time in minutes.
     *
     * @return int
     */
    protected function getReadingTime(): int
    {
        if (!$this->content) {
            return 1;
        }

        $plainText = strip_tags($this->content);
        $wordCount = str_word_count($plainText);
        $readingTime = ceil($wordCount / 200); // Assuming 200 words per minute

        return max(1, $readingTime);
    }
}
