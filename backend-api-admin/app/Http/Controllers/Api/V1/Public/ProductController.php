<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\Public\ProductCollection;
use App\Http\Resources\Api\V1\Public\ProductResource;
use App\Http\Resources\Api\V1\Public\ProductDetailResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    /**
     * Get list of active products with pagination.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()
            ->active()
            ->with(['category']);

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

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter free products
        if ($request->boolean('free')) {
            $query->where('price', 0);
        }

        // Search by keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSorts = ['created_at', 'title', 'price'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        $perPage = min($request->get('per_page', 12), 50);
        $products = $query->paginate($perPage);

        return $this->successPaginated(
            new ProductCollection($products),
            'Daftar produk berhasil dimuat'
        );
    }

    /**
     * Get featured products.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 6), 20);

        $products = Product::query()
            ->active()
            ->with(['category'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $this->success(
            ProductResource::collection($products),
            'Produk unggulan berhasil dimuat'
        );
    }

    /**
     * Get product detail by slug.
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        $product = Product::query()
            ->active()
            ->where('slug', $slug)
            ->with(['category'])
            ->first();

        if (!$product) {
            return $this->notFound('Produk tidak ditemukan');
        }

        return $this->success(
            new ProductDetailResource($product),
            'Detail produk berhasil dimuat'
        );
    }

    /**
     * Get products by category slug.
     *
     * @param string $categorySlug
     * @param Request $request
     * @return JsonResponse
     */
    public function byCategory(string $categorySlug, Request $request): JsonResponse
    {
        $query = Product::query()
            ->active()
            ->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            })
            ->with(['category']);

        $perPage = min($request->get('per_page', 12), 50);
        $products = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->successPaginated(
            new ProductCollection($products),
            'Daftar produk berhasil dimuat'
        );
    }
}
