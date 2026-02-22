<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\Public\WebinarCollection;
use App\Http\Resources\Api\V1\Public\WebinarResource;
use App\Http\Resources\Api\V1\Public\WebinarDetailResource;
use App\Models\Webinar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebinarController extends BaseController
{
    /**
     * Get list of active webinars with pagination.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Webinar::query()
            ->active()
            ->with(['mentor']);

        // Filter upcoming only
        if ($request->boolean('upcoming')) {
            $query->upcoming();
        }

        // Filter past only
        if ($request->boolean('past')) {
            $query->past();
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter free webinars
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

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('scheduled_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('scheduled_at', '<=', $request->end_date);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'scheduled_at');
        $sortOrder = $request->get('sort_order', 'asc');

        $allowedSorts = ['scheduled_at', 'title', 'price', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        $perPage = min($request->get('per_page', 12), 50);
        $webinars = $query->paginate($perPage);

        return $this->successPaginated(
            new WebinarCollection($webinars),
            'Daftar webinar berhasil dimuat'
        );
    }

    /**
     * Get featured/upcoming webinars.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 6), 20);

        $webinars = Webinar::query()
            ->active()
            ->upcoming()
            ->with(['mentor'])
            ->orderBy('scheduled_at', 'asc')
            ->limit($limit)
            ->get();

        return $this->success(
            WebinarResource::collection($webinars),
            'Webinar unggulan berhasil dimuat'
        );
    }

    /**
     * Get webinar detail by slug.
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        $webinar = Webinar::query()
            ->active()
            ->where('slug', $slug)
            ->with(['mentor'])
            ->first();

        if (!$webinar) {
            return $this->notFound('Webinar tidak ditemukan');
        }

        return $this->success(
            new WebinarDetailResource($webinar),
            'Detail webinar berhasil dimuat'
        );
    }

    /**
     * Get upcoming webinars.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function upcoming(Request $request): JsonResponse
    {
        $perPage = min($request->get('per_page', 12), 50);

        $webinars = Webinar::query()
            ->active()
            ->upcoming()
            ->with(['mentor'])
            ->orderBy('scheduled_at', 'asc')
            ->paginate($perPage);

        return $this->successPaginated(
            new WebinarCollection($webinars),
            'Daftar webinar mendatang berhasil dimuat'
        );
    }
}
