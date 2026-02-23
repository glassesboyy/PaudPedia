<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\Public\ArticleCollection;
use App\Http\Resources\Api\V1\Public\ArticleResource;
use App\Http\Resources\Api\V1\Public\ArticleDetailResource;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends BaseController
{
    /**
     * Get list of published articles with pagination.
     *
     * @unauthenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Article::query()
            ->published()
            ->with(['category', 'author']);

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by category slug
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by tag
        if ($request->filled('tag')) {
            $query->byTag($request->tag);
        }

        // Filter by featured
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Search by keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'published_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSorts = ['published_at', 'title', 'view_count', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        $perPage = min($request->get('per_page', 10), 50);
        $articles = $query->paginate($perPage);

        return $this->successPaginated(
            new ArticleCollection($articles),
            'Daftar artikel berhasil dimuat'
        );
    }

    /**
     * Get featured articles.
     *
     * @unauthenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 5), 20);

        $articles = Article::query()
            ->published()
            ->featured()
            ->with(['category', 'author'])
            ->recent()
            ->limit($limit)
            ->get();

        return $this->success(
            ArticleResource::collection($articles),
            'Artikel unggulan berhasil dimuat'
        );
    }

    /**
     * Get popular articles.
     *
     * @unauthenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function popular(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 5), 20);

        $articles = Article::query()
            ->published()
            ->with(['category', 'author'])
            ->popular()
            ->limit($limit)
            ->get();

        return $this->success(
            ArticleResource::collection($articles),
            'Artikel populer berhasil dimuat'
        );
    }

    /**
     * Get recent articles.
     *
     * @unauthenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function recent(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 5), 20);

        $articles = Article::query()
            ->published()
            ->with(['category', 'author'])
            ->recent()
            ->limit($limit)
            ->get();

        return $this->success(
            ArticleResource::collection($articles),
            'Artikel terbaru berhasil dimuat'
        );
    }

    /**
     * Get article detail by slug.
     *
     * @unauthenticated
     * @param string $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        $article = Article::query()
            ->published()
            ->where('slug', $slug)
            ->with(['category', 'author'])
            ->first();

        if (!$article) {
            return $this->notFound('Artikel tidak ditemukan');
        }

        // Increment view count
        $article->incrementViewCount();

        // Get related articles
        $relatedArticles = Article::query()
            ->published()
            ->where('id', '!=', $article->id)
            ->where(function ($query) use ($article) {
                $query->where('category_id', $article->category_id);
                
                // Also get articles with similar tags
                if ($article->tags && is_array($article->tags)) {
                    foreach ($article->tags as $tag) {
                        $query->orWhereJsonContains('tags', $tag);
                    }
                }
            })
            ->with(['category', 'author'])
            ->recent()
            ->limit(4)
            ->get();

        return $this->success(
            [
                'article' => new ArticleDetailResource($article),
                'related_articles' => ArticleResource::collection($relatedArticles),
            ],
            'Detail artikel berhasil dimuat'
        );
    }

    /**
     * Get articles by category slug.
     *
     * @unauthenticated
     * @param string $categorySlug
     * @param Request $request
     * @return JsonResponse
     */
    public function byCategory(string $categorySlug, Request $request): JsonResponse
    {
        $query = Article::query()
            ->published()
            ->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            })
            ->with(['category', 'author'])
            ->recent();

        $perPage = min($request->get('per_page', 10), 50);
        $articles = $query->paginate($perPage);

        return $this->successPaginated(
            new ArticleCollection($articles),
            'Daftar artikel berhasil dimuat'
        );
    }

    /**
     * Get articles by tag.
     *
     * @unauthenticated
     * @param string $tag
     * @param Request $request
     * @return JsonResponse
     */
    public function byTag(string $tag, Request $request): JsonResponse
    {
        $query = Article::query()
            ->published()
            ->byTag($tag)
            ->with(['category', 'author'])
            ->recent();

        $perPage = min($request->get('per_page', 10), 50);
        $articles = $query->paginate($perPage);

        return $this->successPaginated(
            new ArticleCollection($articles),
            'Daftar artikel dengan tag "' . $tag . '" berhasil dimuat'
        );
    }
}
