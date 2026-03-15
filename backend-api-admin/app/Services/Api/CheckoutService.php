<?php

namespace App\Services\Api;

use App\Enums\OrderItemType;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PromoCode;
use App\Models\User;
use App\Services\Content\PromoCodeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(
        protected MidtransService $midtransService,
        protected PromoCodeService $promoCodeService
    ) {}

    /**
     * Process checkout: validate items, create order, generate Midtrans snap token.
     *
     * @param User   $user
     * @param array  $items     Array of ['id' => int, 'type' => string, 'quantity' => int]
     * @param string|null $promoCode
     * @return array{order: Order, snap_token: string}
     *
     * @throws ValidationException
     * @throws \Exception
     */
    public function processCheckout(User $user, array $items, ?string $promoCode = null): array
    {
        DB::beginTransaction();

        try {
            // 1. Validate and resolve each item
            $resolvedItems = $this->resolveItems($user, $items);

            // 2. Calculate subtotal from DB prices
            $subtotal = collect($resolvedItems)->sum(fn ($item) => $item['price'] * $item['quantity']);

            // 3. Apply promo code
            $discount = 0;
            $appliedPromoCode = null;

            if ($promoCode) {
                $promoResult = $this->promoCodeService->validatePromoCode($promoCode, $subtotal);

                if (!$promoResult['valid']) {
                    throw ValidationException::withMessages([
                        'promo_code' => [$promoResult['message']],
                    ]);
                }

                /** @var PromoCode $promo */
                $promo = $promoResult['promo_code'];
                $discount = $promo->calculateDiscount($subtotal);
                $appliedPromoCode = $promo->code;
            }

            $finalAmount = max(0, $subtotal - $discount);

            // 4. Create Order
            $order = Order::create([
                'user_id'          => $user->id,
                'order_number'     => Order::generateOrderNumber(),
                'total_amount'     => $subtotal,
                'discount_amount'  => $discount,
                'final_amount'     => $finalAmount,
                'promo_code'       => $appliedPromoCode,
                'status'           => OrderStatus::PENDING,
                'midtrans_order_id' => 'PAU-' . time() . '-' . strtoupper(substr(md5(uniqid()), 0, 6)),
            ]);

            // 5. Create OrderItems
            foreach ($resolvedItems as $item) {
                $order->items()->create([
                    'item_type'  => $item['type'],
                    'item_id'    => $item['id'],
                    'item_title' => $item['title'],
                    'item_price' => $item['price'],
                    'quantity'   => $item['quantity'],
                    'subtotal'   => $item['price'] * $item['quantity'],
                ]);
            }

            // 6. Increment promo usage
            if ($promoCode && isset($promo)) {
                $promo->incrementUsage();
            }

            // 7. Generate Midtrans snap token
            $order->load('items');
            $snapToken = $this->midtransService->createSnapToken($order);

            DB::commit();

            return [
                'order'      => $order->load('items'),
                'snap_token' => $snapToken,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Validate and resolve cart items against the database.
     *
     * @return array<int, array{id: int, type: string, title: string, price: float, quantity: int}>
     */
    protected function resolveItems(User $user, array $items): array
    {
        $resolved = [];

        foreach ($items as $item) {
            $type = OrderItemType::from($item['type']);
            $modelClass = $type->modelClass();
            $quantity = $item['quantity'] ?? 1;

            // Find the item model
            $model = $modelClass::find($item['id']);

            if (!$model) {
                throw ValidationException::withMessages([
                    'items' => ["{$type->label()} tidak ditemukan."],
                ]);
            }

            // Verify item is active/published
            if (!$this->isItemAvailable($model, $type)) {
                throw ValidationException::withMessages([
                    'items' => ["{$type->label()} \"{$model->title}\" tidak tersedia saat ini."],
                ]);
            }

            // Check for duplicate purchases (courses and webinars should only be purchased once)
            if ($type === OrderItemType::COURSE || $type === OrderItemType::WEBINAR) {
                $quantity = 1; // Force quantity to 1 for course/webinar

                $alreadyPurchased = OrderItem::where('item_type', $type->value)
                    ->where('item_id', $model->id)
                    ->whereHas('order', function ($q) use ($user) {
                        $q->where('user_id', $user->id)
                          ->where('status', OrderStatus::PAID);
                    })
                    ->exists();

                if ($alreadyPurchased) {
                    throw ValidationException::withMessages([
                        'items' => ["Anda sudah memiliki {$type->label()} \"{$model->title}\"."],
                    ]);
                }
            }

            $resolved[] = [
                'id'       => $model->id,
                'type'     => $type->value,
                'title'    => $model->title,
                'price'    => (float) $model->price,
                'quantity' => $quantity,
            ];
        }

        return $resolved;
    }

    /**
     * Check if an item is available for purchase.
     */
    protected function isItemAvailable(mixed $model, OrderItemType $type): bool
    {
        return match ($type) {
            OrderItemType::COURSE  => (bool) $model->is_published,
            OrderItemType::WEBINAR => (bool) $model->is_active,
            OrderItemType::PRODUCT => (bool) $model->is_active,
        };
    }
}
