<?php

namespace App\Services\Content;

use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleService
{
    /**
     * Create a new article
     *
     * @param array $data
     * @return Article
     * @throws \Exception
     */
    public function createArticle(array $data): Article
    {
        DB::beginTransaction();
        try {
            // Generate slug if not provided
            if (!isset($data['slug']) || empty($data['slug'])) {
                $data['slug'] = $this->generateUniqueSlug($data['title']);
            }

            // Set published_at if is_published is true
            if (!empty($data['is_published']) && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            // Create article
            $article = Article::create($data);

            DB::commit();

            return $article->load(['category', 'author']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing article
     *
     * @param Article $article
     * @param array $data
     * @return Article
     * @throws \Exception
     */
    public function updateArticle(Article $article, array $data): Article
    {
        DB::beginTransaction();
        try {
            // Update slug if title changed
            if (isset($data['title']) && $data['title'] !== $article->title) {
                if (!isset($data['slug']) || empty($data['slug'])) {
                    $data['slug'] = $this->generateUniqueSlug($data['title'], $article->id);
                }
            }

            // Set published_at when publishing
            if (!empty($data['is_published']) && empty($article->published_at)) {
                $data['published_at'] = now();
            }

            // Unset published_at if unpublishing
            if (isset($data['is_published']) && !$data['is_published']) {
                $data['published_at'] = null;
            }

            $article->update($data);

            DB::commit();

            return $article->fresh(['category', 'author']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete article (soft delete)
     *
     * @param Article $article
     * @return bool
     * @throws \Exception
     */
    public function deleteArticle(Article $article): bool
    {
        DB::beginTransaction();
        try {
            $article->delete();
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Toggle article featured status
     *
     * @param Article $article
     * @return Article
     * @throws \Exception
     */
    public function toggleFeaturedStatus(Article $article): Article
    {
        DB::beginTransaction();
        try {
            $article->is_featured = !$article->is_featured;
            $article->save();

            DB::commit();

            return $article->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Toggle article published status
     *
     * @param Article $article
     * @return Article
     * @throws \Exception
     */
    public function togglePublishedStatus(Article $article): Article
    {
        DB::beginTransaction();
        try {
            $article->is_published = !$article->is_published;
            
            // Set published_at when publishing
            if ($article->is_published && !$article->published_at) {
                $article->published_at = now();
            }
            
            // Clear published_at when unpublishing
            if (!$article->is_published) {
                $article->published_at = null;
            }
            
            $article->save();

            DB::commit();

            return $article->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Generate unique slug
     *
     * @param string $title
     * @param int|null $ignoreId
     * @return string
     */
    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     *
     * @param string $slug
     * @param int|null $ignoreId
     * @return bool
     */
    private function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $query = Article::where('slug', $slug);
        
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }
        
        return $query->exists();
    }

    /**
     * Get article statistics
     *
     * @param Article $article
     * @return array
     */
    public function getArticleStatistics(Article $article): array
    {
        return [
            'view_count' => $article->view_count,
            'is_featured' => $article->is_featured,
            'is_published' => $article->is_published,
            'published_date' => $article->formatted_published_date,
        ];
    }
}
