<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\Public\MentorCollection;
use App\Http\Resources\Api\V1\Public\MentorResource;
use App\Http\Resources\Api\V1\Public\MentorDetailResource;
use App\Models\Mentor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MentorController extends BaseController
{
    /**
     * Get list of active mentors with pagination.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Mentor::query()
            ->active()
            ->withCount(['courses' => fn($q) => $q->published(), 'webinars' => fn($q) => $q->active()]);

        // Filter by expertise
        if ($request->filled('expertise')) {
            $query->where('expertise', 'like', '%' . $request->expertise . '%');
        }

        // Search by name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('bio', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');

        $allowedSorts = ['name', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        $perPage = min($request->get('per_page', 12), 50);
        $mentors = $query->paginate($perPage);

        return $this->successPaginated(
            new MentorCollection($mentors),
            'Daftar mentor berhasil dimuat'
        );
    }

    /**
     * Get mentor detail by ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $mentor = Mentor::query()
            ->active()
            ->where('id', $id)
            ->with([
                'courses' => fn($q) => $q->published()->with(['category'])->limit(6),
                'webinars' => fn($q) => $q->active()->upcoming()->limit(6),
            ])
            ->withCount(['courses' => fn($q) => $q->published(), 'webinars' => fn($q) => $q->active()])
            ->first();

        if (!$mentor) {
            return $this->notFound('Mentor tidak ditemukan');
        }

        return $this->success(
            new MentorDetailResource($mentor),
            'Detail mentor berhasil dimuat'
        );
    }

    /**
     * Get featured mentors.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 6), 20);

        $mentors = Mentor::query()
            ->active()
            ->withCount(['courses' => fn($q) => $q->published(), 'webinars' => fn($q) => $q->active()])
            ->orderBy('courses_count', 'desc')
            ->limit($limit)
            ->get();

        return $this->success(
            MentorResource::collection($mentors),
            'Mentor unggulan berhasil dimuat'
        );
    }
}
