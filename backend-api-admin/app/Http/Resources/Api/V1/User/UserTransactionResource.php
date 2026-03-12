<?php

namespace App\Http\Resources\Api\V1\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User Transaction Resource — order with items for transaction history.
 *
 * Used in: GET /api/v1/user/transactions
 */
class UserTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_color' => $this->status->color(),
            'total_amount' => (float) $this->total_amount,
            'discount_amount' => (float) $this->discount_amount,
            'final_amount' => (float) $this->final_amount,
            'payment_method' => $this->payment_method,
            'items' => $this->whenLoaded('items', function () {
                return $this->items->map(fn($item) => [
                    'id' => $item->id,
                    'type' => $item->item_type->value,
                    'type_label' => $item->item_type->label(),
                    'title' => $item->item_title,
                    'price' => (float) $item->item_price,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                ]);
            }),
            'paid_at' => $this->paid_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'created_date' => $this->created_at?->format('d M Y'),
        ];
    }
}
