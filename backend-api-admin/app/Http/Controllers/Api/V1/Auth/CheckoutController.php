<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\Api\V1\CheckoutRequest;
use App\Http\Resources\Api\V1\User\UserTransactionResource;
use App\Services\Api\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CheckoutController extends BaseController
{
    public function __construct(
        protected CheckoutService $checkoutService
    ) {}

    /**
     * Create an order and return a Midtrans Snap token.
     *
     * POST /api/v1/user/checkout
     */
    public function store(CheckoutRequest $request): JsonResponse
    {
        try {
            $result = $this->checkoutService->processCheckout(
                $request->user(),
                $request->validated('items'),
                $request->validated('promo_code')
            );

            return $this->created([
                'order'      => new UserTransactionResource($result['order']),
                'snap_token' => $result['snap_token'],
            ], 'Pesanan berhasil dibuat.');
        } catch (ValidationException $e) {
            return $this->validationError($e->errors());
        } catch (\Exception $e) {
            return $this->error('Gagal membuat pesanan: ' . $e->getMessage(), 500);
        }
    }
}
