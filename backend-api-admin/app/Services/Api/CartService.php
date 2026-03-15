<?php

namespace App\Services\Api;

use App\Models\PromoCode;
use App\Services\Content\PromoCodeService;

class CartService
{
    public function __construct(
        protected PromoCodeService $promoCodeService
    ) {}

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
}
