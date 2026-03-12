<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Enums\OrderItemType;
use App\Enums\OrderStatus;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\User\UserCertificateResource;
use App\Http\Resources\Api\V1\User\UserCourseResource;
use App\Http\Resources\Api\V1\User\UserProductResource;
use App\Http\Resources\Api\V1\User\UserTransactionResource;
use App\Http\Resources\Api\V1\User\UserWebinarResource;
use App\Models\CourseEnrollment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserDashboardController extends BaseController
{
    /**
     * FR-UA-08: My Courses — enrolled courses with progress.
     *
     * GET /api/v1/user/courses
     */
    public function courses(Request $request): JsonResponse
    {
        $user = $request->user();

        $enrollments = CourseEnrollment::query()
            ->where('user_id', $user->id)
            ->with(['course.mentor', 'course.category'])
            ->orderByRaw('completed_at IS NOT NULL, completed_at DESC')
            ->orderBy('enrolled_at', 'desc')
            ->paginate($request->input('per_page', 12));

        return $this->successPaginated(
            UserCourseResource::collection($enrollments),
            'Daftar kursus berhasil dimuat'
        );
    }

    /**
     * FR-UA-09: My Products — purchased products with download link.
     *
     * GET /api/v1/user/products
     */
    public function products(Request $request): JsonResponse
    {
        $user = $request->user();

        $items = OrderItem::query()
            ->where('item_type', OrderItemType::PRODUCT)
            ->whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('status', OrderStatus::PAID);
            })
            ->with(['order', 'item' => function ($morphTo) {
                $morphTo->morphWith([
                    Product::class => ['category'],
                ]);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 12));

        return $this->successPaginated(
            UserProductResource::collection($items),
            'Daftar produk berhasil dimuat'
        );
    }

    /**
     * FR-UA-09: Download a purchased product file.
     *
     * GET /api/v1/user/products/{id}/download
     */
    public function downloadProduct(Request $request, int $id): JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $user = $request->user();

        // Verify ownership through paid order
        $hasPurchased = OrderItem::query()
            ->where('item_type', OrderItemType::PRODUCT)
            ->where('item_id', $id)
            ->whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('status', OrderStatus::PAID);
            })
            ->exists();

        if (!$hasPurchased) {
            return $this->forbidden('Anda belum membeli produk ini');
        }

        $product = Product::findOrFail($id);

        if (!$product->file_url || !Storage::disk('local')->exists($product->file_url)) {
            return $this->notFound('File produk tidak ditemukan');
        }

        $filename = $product->slug . '.' . pathinfo($product->file_url, PATHINFO_EXTENSION);
        $fullPath = Storage::disk('local')->path($product->file_url);

        return response()->download($fullPath, $filename);
    }

    /**
     * FR-UA-10: My Webinars — registered webinars with zoom link.
     *
     * GET /api/v1/user/webinars
     */
    public function webinars(Request $request): JsonResponse
    {
        $user = $request->user();

        $items = OrderItem::query()
            ->where('item_type', OrderItemType::WEBINAR)
            ->whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('status', OrderStatus::PAID);
            })
            ->with(['item' => function ($morphTo) {
                $morphTo->morphWith([
                    \App\Models\Webinar::class => ['mentor'],
                ]);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 12));

        return $this->successPaginated(
            UserWebinarResource::collection($items),
            'Daftar webinar berhasil dimuat'
        );
    }

    /**
     * FR-UA-11: My Certificates — certificates from completed courses.
     *
     * GET /api/v1/user/certificates
     */
    public function certificates(Request $request): JsonResponse
    {
        $user = $request->user();

        $enrollments = CourseEnrollment::query()
            ->where('user_id', $user->id)
            ->whereNotNull('certificate_url')
            ->whereNotNull('completed_at')
            ->with('course')
            ->orderBy('completed_at', 'desc')
            ->paginate($request->input('per_page', 12));

        return $this->successPaginated(
            UserCertificateResource::collection($enrollments),
            'Daftar sertifikat berhasil dimuat'
        );
    }

    /**
     * FR-UA-12: Transaction History — all user orders.
     *
     * GET /api/v1/user/transactions
     */
    public function transactions(Request $request): JsonResponse
    {
        $user = $request->user();

        $orders = Order::query()
            ->where('user_id', $user->id)
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        return $this->successPaginated(
            UserTransactionResource::collection($orders),
            'Riwayat transaksi berhasil dimuat'
        );
    }

    /**
     * FR-UA-12: Transaction Detail — single order detail.
     *
     * GET /api/v1/user/transactions/{id}
     */
    public function transactionDetail(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $order = Order::query()
            ->where('user_id', $user->id)
            ->with('items')
            ->find($id);

        if (!$order) {
            return $this->notFound('Transaksi tidak ditemukan');
        }

        return $this->success(
            new UserTransactionResource($order),
            'Detail transaksi berhasil dimuat'
        );
    }
}
