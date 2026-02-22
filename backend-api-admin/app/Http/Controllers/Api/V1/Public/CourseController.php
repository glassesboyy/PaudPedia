<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\Public\CourseCollection;
use App\Http\Resources\Api\V1\Public\CourseResource;
use App\Http\Resources\Api\V1\Public\CourseDetailResource;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends BaseController
{
    /**
     * Get list of published courses with pagination.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Course::query()
            ->published()
            ->with(['mentor', 'category'])
            ->withCount('modules');

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

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter free courses
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

        // Filter by mentor
        if ($request->filled('mentor_id')) {
            $query->where('mentor_id', $request->mentor_id);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSorts = ['created_at', 'title', 'price', 'duration_hours'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        $perPage = min($request->get('per_page', 12), 50);
        $courses = $query->paginate($perPage);

        return $this->successPaginated(
            new CourseCollection($courses),
            'Daftar kursus berhasil dimuat'
        );
    }

    /**
     * Get featured courses.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 6), 20);

        $courses = Course::query()
            ->published()
            ->with(['mentor', 'category'])
            ->withCount('modules')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $this->success(
            CourseResource::collection($courses),
            'Kursus unggulan berhasil dimuat'
        );
    }

    /**
     * Get course detail by slug.
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        $course = Course::query()
            ->published()
            ->where('slug', $slug)
            ->with([
                'mentor',
                'category',
                'modules' => function ($query) {
                    $query->orderBy('order');
                },
                'modules.lessons' => function ($query) {
                    $query->orderBy('order');
                },
            ])
            ->withCount(['modules', 'enrollments'])
            ->first();

        if (!$course) {
            return $this->notFound('Kursus tidak ditemukan');
        }

        return $this->success(
            new CourseDetailResource($course),
            'Detail kursus berhasil dimuat'
        );
    }

    /**
     * Get courses by category slug.
     *
     * @param string $categorySlug
     * @param Request $request
     * @return JsonResponse
     */
    public function byCategory(string $categorySlug, Request $request): JsonResponse
    {
        $query = Course::query()
            ->published()
            ->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            })
            ->with(['mentor', 'category'])
            ->withCount('modules');

        $perPage = min($request->get('per_page', 12), 50);
        $courses = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->successPaginated(
            new CourseCollection($courses),
            'Daftar kursus berhasil dimuat'
        );
    }
}
