<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Enums\CategoryType;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\Public\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    /**
     * Get all categories.
     *
     * @unauthenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Category::query();

        // Filter by type
        if ($request->filled('type')) {
            $type = CategoryType::tryFrom($request->type);
            if ($type) {
                $query->byType($type);
            }
        }

        $categories = $query->orderBy('name')->get();

        return $this->success(
            CategoryResource::collection($categories),
            'Daftar kategori berhasil dimuat'
        );
    }

    /**
     * Get course categories.
     *
     * @unauthenticated
     * @return JsonResponse
     */
    public function courses(): JsonResponse
    {
        $categories = Category::courseCategories()
            ->withCount(['courses' => fn($q) => $q->published()])
            ->orderBy('name')
            ->get();

        return $this->success(
            CategoryResource::collection($categories),
            'Daftar kategori kursus berhasil dimuat'
        );
    }

    /**
     * Get product categories.
     *
     * @unauthenticated
     * @return JsonResponse
     */
    public function products(): JsonResponse
    {
        $categories = Category::productCategories()
            ->withCount(['products' => fn($q) => $q->active()])
            ->orderBy('name')
            ->get();

        return $this->success(
            CategoryResource::collection($categories),
            'Daftar kategori produk berhasil dimuat'
        );
    }

    /**
     * Get article categories.
     *
     * @unauthenticated
     * @return JsonResponse
     */
    public function articles(): JsonResponse
    {
        $categories = Category::articleCategories()
            ->withCount(['articles' => fn($q) => $q->published()])
            ->orderBy('name')
            ->get();

        return $this->success(
            CategoryResource::collection($categories),
            'Daftar kategori artikel berhasil dimuat'
        );
    }
}
