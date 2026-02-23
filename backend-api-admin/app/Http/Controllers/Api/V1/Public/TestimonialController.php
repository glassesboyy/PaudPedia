<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Api\V1\Public\StoreTestimonialRequest;
use App\Http\Resources\Api\V1\Public\TestimonialCollection;
use App\Http\Resources\Api\V1\Public\TestimonialResource;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestimonialController extends BaseController
{
    /**
     * Get list of approved testimonials with pagination.
     *
     * @unauthenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Testimonial::query()
            ->approved()
            ->with(['user']);

        // Filter by featured
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Filter by minimum rating
        if ($request->filled('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSorts = ['created_at', 'rating'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        $perPage = min($request->get('per_page', 10), 50);
        $testimonials = $query->paginate($perPage);

        return $this->successPaginated(
            new TestimonialCollection($testimonials),
            'Daftar testimonial berhasil dimuat'
        );
    }

    /**
     * Get featured testimonials.
     *
     * @unauthenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 6), 20);

        $testimonials = Testimonial::query()
            ->approved()
            ->featured()
            ->with(['user'])
            ->orderBy('rating', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $this->success(
            TestimonialResource::collection($testimonials),
            'Testimonial unggulan berhasil dimuat'
        );
    }

    /**
     * Store a new testimonial (requires authentication).
     *
     * @param StoreTestimonialRequest $request
     * @return JsonResponse
     */
    public function store(StoreTestimonialRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        // Associate with authenticated user
        $data['user_id'] = $request->user()->id;

        // Set default values
        $data['is_approved'] = false;
        $data['is_featured'] = false;

        $testimonial = Testimonial::create($data);

        return $this->created(
            new TestimonialResource($testimonial),
            'Testimonial berhasil dikirim dan menunggu persetujuan'
        );
    }
}
