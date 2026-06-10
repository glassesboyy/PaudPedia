<?php

namespace App\Http\Controllers\Api\V1\Webhook;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Order;
use App\Models\SubscriptionOrder;
use App\Services\Api\MidtransService;
use App\Services\Api\OrderProcessingService;
use App\Services\Api\SubscriptionPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends BaseController
{
    public function __construct(
        protected MidtransService $midtransService,
        protected OrderProcessingService $orderProcessingService,
        protected SubscriptionPaymentService $subscriptionPaymentService
    ) {}

    /**
     * Handle Midtrans payment notification webhook.
     *
     * POST /api/v1/webhooks/midtrans
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $notification = $this->midtransService->handleNotification();

            $orderId           = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus       = $notification->fraud_status ?? null;
            $paymentType       = $notification->payment_type ?? null;
            $statusCode        = $notification->status_code ?? '';
            $grossAmount       = $notification->gross_amount ?? '';
            $signatureKey      = $notification->signature_key ?? '';

            // Verify signature
            if (!$this->midtransService->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
                Log::warning('Midtrans webhook: Invalid signature', ['order_id' => $orderId]);
                return $this->error('Invalid signature', 403);
            }

            // Detect subscription order by SUB- prefix
            if (str_starts_with($orderId, 'SUB-')) {
                return $this->handleSubscriptionWebhook($orderId, $transactionStatus, $paymentType, $notification);
            }

            // Find order by midtrans_order_id
            $order = Order::where('midtrans_order_id', $orderId)->first();

            if (!$order) {
                Log::warning('Midtrans webhook: Order not found', ['order_id' => $orderId]);
                return $this->notFound('Order tidak ditemukan');
            }

            // Idempotency: skip if already processed
            if ($order->status !== OrderStatus::PENDING) {
                return $this->success(null, 'Order sudah diproses sebelumnya.');
            }

            // Store Midtrans data
            $order->midtrans_transaction_id = $notification->transaction_id ?? null;
            $order->payment_method = $paymentType;
            $order->save();

            // Map Midtrans status -> internal PaymentStatus -> OrderStatus
            $paymentStatus = PaymentStatus::fromMidtrans($transactionStatus);

            if ($paymentStatus->isSuccessful()) {
                // For credit card: check fraud status
                if ($paymentType === 'credit_card' && $fraudStatus === 'challenge') {
                    Log::info('Midtrans webhook: Credit card fraud challenge', ['order_id' => $orderId]);
                    return $this->success(null, 'Menunggu review fraud.');
                }

                $this->orderProcessingService->processSuccessfulPayment($order);
            } elseif ($paymentStatus->isFailed()) {
                $this->orderProcessingService->processFailedPayment($order);
            } elseif ($paymentStatus->isCancelled()) {
                $order->markAsCancelled();
            }

            Log::info('Midtrans webhook: Processed', [
                'order_id' => $orderId,
                'status'   => $transactionStatus,
                'result'   => $order->status->value,
            ]);

            return $this->success(null, 'Notifikasi berhasil diproses.');
        } catch (\Exception $e) {
            Log::error('Midtrans webhook error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return $this->error('Internal error', 500);
        }
    }

    /**
     * Handle subscription-specific webhook (SUB- prefix orders).
     */
    protected function handleSubscriptionWebhook(string $orderId, string $transactionStatus, ?string $paymentType, $notification): JsonResponse
    {
        $subOrder = SubscriptionOrder::where('midtrans_order_id', $orderId)->first();

        if (!$subOrder) {
            Log::warning('Midtrans webhook: Subscription order not found', ['order_id' => $orderId]);
            return $this->notFound('Subscription order tidak ditemukan');
        }

        if ($subOrder->isPaid()) {
            return $this->success(null, 'Subscription order sudah diproses.');
        }

        $paymentStatus = PaymentStatus::fromMidtrans($transactionStatus);

        if ($paymentStatus->isSuccessful()) {
            $this->subscriptionPaymentService->handlePaymentSuccess(
                $subOrder,
                $notification->transaction_id ?? null,
                $paymentType
            );
            Log::info('Midtrans webhook: Subscription upgraded', ['order_id' => $orderId, 'school_id' => $subOrder->school_id]);
        } elseif ($paymentStatus->isFailed() || $paymentStatus->isCancelled()) {
            $subOrder->markAsFailed();
            Log::info('Midtrans webhook: Subscription payment failed', ['order_id' => $orderId]);
        }

        return $this->success(null, 'Subscription notification diproses.');
    }
}

