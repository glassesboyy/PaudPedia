<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Api\V1\ValidatePromoRequest;
use App\Services\Api\CartService;
use Illuminate\Http\JsonResponse;

class CartController extends BaseController
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Validate a promo code.
     *
     * POST /api/v1/user/cart/validate-promo
     */
    public function validatePromo(ValidatePromoRequest $request): JsonResponse
    {
        $result = $this->cartService->validatePromo(
            $request->validated('code'),
            (float) $request->validated('subtotal')
        );

        if (!$result['valid']) {
            return $this->error($result['message'], 422);
        }

        return $this->success($result, 'Kode promo berhasil divalidasi.');
    }
}
