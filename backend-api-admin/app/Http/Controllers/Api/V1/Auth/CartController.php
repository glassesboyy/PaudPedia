<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Api\V1\AddCartItemRequest;
use App\Http\Requests\Api\V1\UpdateCartItemRequest;
use App\Http\Requests\Api\V1\RemoveCartItemRequest;
use App\Http\Requests\Api\V1\ValidatePromoRequest;
use App\Services\Api\CartService;
use Illuminate\Http\JsonResponse;

class CartController extends BaseController
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Get the authenticated user's cart.
     *
     * GET /api/v1/user/cart
     */
    public function index(): JsonResponse
    {
        $result = $this->cartService->getCart($this->user());

        return $this->success($result);
    }

    /**
     * Add an item to the cart.
     *
     * POST /api/v1/user/cart/items
     */
    public function addItem(AddCartItemRequest $request): JsonResponse
    {
        $result = $this->cartService->addItem(
            $this->user(),
            $request->validated('type'),
            (int) $request->validated('id'),
            (int) ($request->validated('quantity') ?? 1)
        );

        return $this->success($result, 'Item berhasil ditambahkan ke keranjang.');
    }

    /**
     * Update item quantity.
     *
     * PUT /api/v1/user/cart/items
     */
    public function updateItem(UpdateCartItemRequest $request): JsonResponse
    {
        $result = $this->cartService->updateItem(
            $this->user(),
            $request->validated('type'),
            (int) $request->validated('id'),
            (int) $request->validated('quantity')
        );

        return $this->success($result, 'Keranjang berhasil diperbarui.');
    }

    /**
     * Remove an item from the cart.
     *
     * DELETE /api/v1/user/cart/items
     */
    public function removeItem(RemoveCartItemRequest $request): JsonResponse
    {
        $result = $this->cartService->removeItem(
            $this->user(),
            $request->validated('type'),
            (int) $request->validated('id')
        );

        return $this->success($result, 'Item berhasil dihapus dari keranjang.');
    }

    /**
     * Clear all cart items.
     *
     * DELETE /api/v1/user/cart
     */
    public function clear(): JsonResponse
    {
        $this->cartService->clearCart($this->user());

        return $this->success(null, 'Keranjang berhasil dikosongkan.');
    }

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

    /**
     * Get the authenticated user.
     */
    protected function user(): \App\Models\User
    {
        return request()->user();
    }
}
