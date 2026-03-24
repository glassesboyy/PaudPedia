<?php

namespace App\Services\Api;

use App\Enums\OrderItemType;
use App\Models\Order;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Create a Midtrans Snap transaction for the given order.
     * Returns an array containing the snap token and the redirect URL.
     */
    public function createSnapTransaction(Order $order): array
    {
        $order->loadMissing(['items', 'user']);

        $itemDetails = $order->items->map(function ($item) {
            $type = $item->item_type instanceof OrderItemType
                ? $item->item_type->value
                : (string) $item->item_type;

            return [
                'id'       => $type . '-' . $item->item_id,
                'price'    => (int) $item->item_price,
                'quantity' => $item->quantity,
                'name'     => mb_substr($item->item_title, 0, 50),
            ];
        })->toArray();

        // Add discount as a negative line item if applicable
        if ($order->discount_amount > 0) {
            $itemDetails[] = [
                'id'       => 'DISCOUNT',
                'price'    => (int) -$order->discount_amount,
                'quantity' => 1,
                'name'     => 'Diskon' . ($order->promo_code ? ' (' . $order->promo_code . ')' : ''),
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $order->midtrans_order_id,
                'gross_amount' => (int) $order->final_amount,
            ],
            'item_details'     => $itemDetails,
            'customer_details' => [
                'first_name' => $order->user->name,
                'email'      => $order->user->email,
                'phone'      => $order->user->phone ?? '',
            ],
            'callbacks' => [
                'finish' => rtrim(config('app.frontend_url', 'http://localhost:3000'), '/') . '/payment/finish'
            ]
        ];

        $snap = Snap::createTransaction($params);

        return [
            'token' => $snap->token,
            'redirect_url' => $snap->redirect_url,
        ];
    }

    /**
     * Handle an incoming Midtrans notification.
     */
    public function handleNotification(): Notification
    {
        return new Notification();
    }

    /**
     * Verify the signature key from a Midtrans notification.
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $serverKey = config('midtrans.server_key');
        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return $signatureKey === $expected;
    }
}
