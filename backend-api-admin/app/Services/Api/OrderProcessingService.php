<?php

namespace App\Services\Api;

use App\Enums\OrderItemType;
use App\Enums\OrderStatus;
use App\Mail\OrderPaidMail;
use App\Models\CourseEnrollment;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderProcessingService
{
    /**
     * Process a successful payment: mark order as paid, distribute access, send email.
     */
    public function processSuccessfulPayment(Order $order): void
    {
        DB::beginTransaction();

        try {
            // 1. Mark order as paid
            $order->status = OrderStatus::PAID;
            $order->paid_at = now();
            $order->save();

            // 2. Process each order item
            $order->loadMissing('items');

            foreach ($order->items as $item) {
                $type = $item->item_type instanceof OrderItemType
                    ? $item->item_type
                    : OrderItemType::from($item->item_type);

                if ($type->requiresEnrollment()) {
                    $this->createCourseEnrollment($order->user_id, $item->item_id);
                }
                // Webinar and Product access is verified via OrderItem query
                // (existing pattern in UserDashboardController)
            }

            DB::commit();

            // 3. Send email notification (outside transaction, non-critical)
            try {
                Mail::to($order->user->email)->queue(new OrderPaidMail($order));
            } catch (\Exception $e) {
                Log::error('Failed to queue order paid email', [
                    'order_id' => $order->id,
                    'error'    => $e->getMessage(),
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process successful payment', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Process a failed payment.
     */
    public function processFailedPayment(Order $order): void
    {
        $order->status = OrderStatus::FAILED;
        $order->save();
    }

    /**
     * Process an expired payment.
     */
    public function processExpiredPayment(Order $order): void
    {
        $order->status = OrderStatus::EXPIRED;
        $order->save();
    }

    /**
     * Create a course enrollment for the user.
     */
    protected function createCourseEnrollment(int $userId, int $courseId): void
    {
        CourseEnrollment::firstOrCreate(
            [
                'user_id'   => $userId,
                'course_id' => $courseId,
            ],
            [
                'enrolled_at'         => now(),
                'progress_percentage' => 0,
            ]
        );
    }
}
