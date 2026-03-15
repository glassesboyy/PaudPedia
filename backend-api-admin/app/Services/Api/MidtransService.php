<?php

namespace App\Services\Api;

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
     * Create a Midtrans Snap token for the given order.
     */
    public function createSnapToken(Order $order): string
    {
        $order->loadMissing(['items', 'user']);

        $itemDetails = $order->items->map(function ($item) {
            return [
                'id'       => $item->item_type . '-' . $item->item_id,
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
        ];

        return Snap::getSnapToken($params);
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
