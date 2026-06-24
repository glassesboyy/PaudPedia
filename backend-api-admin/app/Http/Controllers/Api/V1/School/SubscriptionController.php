<?php

namespace App\Http\Controllers\Api\V1\School;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SubscriptionOrder;
use App\Services\Api\SubscriptionPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionPaymentService $subscriptionService
    ) {}

    /**
     * GET /api/v1/schools/{id}/subscription
     * 
     * Get current subscription info, usage, and limits.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $school = School::findOrFail($id);

        // Authorization: must be a member
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->first();

        if (!$membership) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $info = $this->subscriptionService->getSubscriptionInfo($school);

        return response()->json($info);
    }

    /**
     * POST /api/v1/schools/{id}/subscription/upgrade
     * 
     * Initiate Midtrans payment for Pro Plan upgrade.
     */
    public function upgrade(Request $request, int $id): JsonResponse
    {
        $school = School::findOrFail($id);

        // Authorization: headmaster only
        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->where('role_type', 'headmaster')
            ->first();

        if (!$membership) {
            return response()->json(['message' => 'Hanya Kepala Sekolah yang dapat melakukan upgrade.'], 403);
        }

        $request->validate([
            'duration_months' => 'nullable|integer|in:1,3,6,12',
        ]);
        
        $durationMonths = (int) $request->input('duration_months', 1);

        try {
            $order = $this->subscriptionService->createUpgradeTransaction($school, $durationMonths);

            return response()->json([
                'message' => 'Silakan selesaikan pembayaran.',
                'snap_token' => $order->snap_token,
                'order_id' => $order->midtrans_order_id,
                'amount' => $order->amount,
                'amount_formatted' => $order->formatted_amount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memproses pembayaran. Silakan coba lagi.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * GET /api/v1/schools/{id}/subscription/payment-history
     * 
     * List subscription payment history.
     */
    public function paymentHistory(Request $request, int $id): JsonResponse
    {
        $school = School::findOrFail($id);

        $membership = $request->user()->schoolMemberships()
            ->where('school_id', $school->id)
            ->where('role_type', 'headmaster')
            ->first();

        if (!$membership) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $orders = SubscriptionOrder::where('school_id', $school->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5); // Show 5 per page

        $orders->getCollection()->transform(fn ($order) => [
            'id' => $order->id,
            'amount' => $order->amount,
            'amount_formatted' => $order->formatted_amount,
            'status' => $order->status->value,
            'status_label' => $order->status->label(),
            'duration_months' => $order->duration_months,
            'payment_method' => $order->payment_method,
            'paid_at' => $order->paid_at?->toISOString(),
            'created_at' => $order->created_at->toISOString(),
            'snap_token' => $order->snap_token,
        ]);

        return response()->json($orders);
    }
}
