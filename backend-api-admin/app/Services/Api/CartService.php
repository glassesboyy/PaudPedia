<?php

namespace App\Services\Api;

use App\Enums\OrderItemType;
use App\Enums\OrderStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CourseEnrollment;
use App\Models\OrderItem;
use App\Models\PromoCode;
use App\Models\User;
use App\Services\Content\PromoCodeService;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function __construct(
        protected PromoCodeService $promoCodeService
    ) {}

    /**
     * Get or create the cart for a user, with resolved item details.
     *
     * @return array{items: array, subtotal: float}
     */
    public function getCart(User $user): array
    {
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $cartItems = $cart->items()->get();

        $items = [];
        foreach ($cartItems as $cartItem) {
            $resolved = $this->resolveItem($cartItem);
            if ($resolved) {
                $items[] = $resolved;
            } else {
                // Item no longer exists or is inactive — remove from cart
                $cartItem->delete();
            }
        }

        return [
            'items'    => $items,
            'subtotal' => collect($items)->sum(fn ($i) => $i['price'] * $i['quantity']),
        ];
    }

    /**
     * Add an item to the user's cart.
     *
     * @return array{items: array, subtotal: float}
     * @throws ValidationException
     */
    public function addItem(User $user, string $itemType, int $itemId, int $quantity = 1): array
    {
        $type = OrderItemType::from($itemType);
        $modelClass = $type->modelClass();
        $model = $modelClass::find($itemId);

        if (!$model) {
            throw ValidationException::withMessages([
                'item_id' => ["{$type->label()} tidak ditemukan."],
            ]);
        }

        if (!$this->isItemAvailable($model, $type)) {
            throw ValidationException::withMessages([
                'item_id' => ["{$type->label()} \"{$model->title}\" tidak tersedia saat ini."],
            ]);
        }

        // Check if user already purchased this course, webinar, or digital product
        if ($type === OrderItemType::COURSE || $type === OrderItemType::WEBINAR || $type === OrderItemType::PRODUCT) {
            $alreadyPurchased = OrderItem::where('item_type', $type->value)
                ->where('item_id', $model->id)
                ->whereHas('order', function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->where('status', OrderStatus::PAID);
                })
                ->exists();

            if ($type === OrderItemType::COURSE && !$alreadyPurchased) {
                $alreadyPurchased = CourseEnrollment::where('user_id', $user->id)
                    ->where('course_id', $model->id)
                    ->exists();
            }

            if ($alreadyPurchased) {
                throw ValidationException::withMessages([
                    'item_id' => ["Anda sudah memiliki {$type->label()} \"{$model->title}\"."],
                ]);
            }
        }

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // All digital items (Course, Webinar, Product): quantity always 1, no duplicates
        $quantity = 1;
        $existing = $cart->items()
            ->where('item_type', $type->value)
            ->where('item_id', $itemId)
            ->first();

        if ($existing) {
            throw ValidationException::withMessages([
                'item_id' => ["{$type->label()} \"{$model->title}\" sudah ada di keranjang."],
            ]);
        }

        $cart->items()->create([
            'item_type' => $type->value,
            'item_id'   => $itemId,
            'quantity'  => $quantity,
        ]);

        return $this->getCart($user);
    }

    /**
     * Update item quantity.
     *
     * @return array{items: array, subtotal: float}
     */
    public function updateItem(User $user, string $itemType, int $itemId, int $quantity): array
    {
        $cart = Cart::where('user_id', $user->id)->first();

        if ($cart) {
            $cartItem = $cart->items()
                ->where('item_type', $itemType)
                ->where('item_id', $itemId)
                ->first();

            if ($cartItem) {
                if ($quantity <= 0) {
                    $cartItem->delete();
                } else {
                    $cartItem->update(['quantity' => $quantity]);
                }
            }
        }

        return $this->getCart($user);
    }

    /**
     * Remove an item from the user's cart.
     *
     * @return array{items: array, subtotal: float}
     */
    public function removeItem(User $user, string $itemType, int $itemId): array
    {
        $cart = Cart::where('user_id', $user->id)->first();

        if ($cart) {
            $cart->items()
                ->where('item_type', $itemType)
                ->where('item_id', $itemId)
                ->delete();
        }

        return $this->getCart($user);
    }

    /**
     * Clear all items from the user's cart.
     */
    public function clearCart(User $user): void
    {
        $cart = Cart::where('user_id', $user->id)->first();

        if ($cart) {
            $cart->items()->delete();
        }
    }

    /**
     * Validate a promo code against the given subtotal.
     *
     * @return array{valid: bool, message: string|null, discount: float, discount_display: string, promo_code: array|null}
     */
    public function validatePromo(string $code, float $subtotal): array
    {
        $result = $this->promoCodeService->validatePromoCode($code, $subtotal);

        if (!$result['valid']) {
            return [
                'valid'            => false,
                'message'          => $result['message'],
                'discount'         => 0,
                'discount_display' => '',
                'promo_code'       => null,
            ];
        }

        /** @var PromoCode $promo */
        $promo = $result['promo_code'];
        $discount = $promo->calculateDiscount($subtotal);

        return [
            'valid'            => true,
            'message'          => null,
            'discount'         => round($discount, 2),
            'discount_display' => $promo->discount_display,
            'promo_code'       => [
                'code'           => $promo->code,
                'discount_type'  => $promo->discount_type->value,
                'discount_value' => $promo->discount_value,
            ],
        ];
    }

    /**
     * Resolve a CartItem into its full display data.
     *
     * @return array{id: int, type: string, name: string, slug: string, price: float, thumbnail: string, quantity: int}|null
     */
    protected function resolveItem(CartItem $cartItem): ?array
    {
        $type = $cartItem->item_type;
        $modelClass = $type->modelClass();
        $model = $modelClass::find($cartItem->item_id);

        if (!$model || !$this->isItemAvailable($model, $type)) {
            return null;
        }

        return [
            'id'        => $model->id,
            'type'      => $type->value,
            'name'      => $model->title,
            'slug'      => $model->slug,
            'price'     => (float) $model->price,
            'thumbnail' => $model->thumbnail_url ? asset('storage/' . $model->thumbnail_url) : '',
            'quantity'  => $cartItem->quantity,
        ];
    }

    /**
     * Check if an item is available for the cart.
     */
    protected function isItemAvailable(mixed $model, OrderItemType $type): bool
    {
        return match ($type) {
            OrderItemType::COURSE  => (bool) $model->is_published,
            OrderItemType::WEBINAR => (bool) $model->is_active
                && (!$model->scheduled_at || $model->scheduled_at->isFuture())
                && (!$model->max_participants || $model->total_purchases < $model->max_participants),
            OrderItemType::PRODUCT => (bool) $model->is_active,
        };
    }
}
