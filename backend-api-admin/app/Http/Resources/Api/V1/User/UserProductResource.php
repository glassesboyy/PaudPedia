<?php

namespace App\Http\Resources\Api\V1\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User Product Resource — purchased product with download info.
 *
 * Used in: GET /api/v1/user/products
 * Wraps an OrderItem of type=product, enriched with the related Product model.
 */
class UserProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->item;
        $order = $this->order;

        return [
            'id' => $this->id,
            'product_id' => $product?->id,
            'title' => $this->item_title,
            'slug' => $product?->slug,
            'thumbnail_url' => $product?->thumbnail_url ? asset('storage/' . $product->thumbnail_url) : null,
            'category' => $product?->relationLoaded('category') && $product?->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
            ] : null,
            'file_info' => $this->getFileInfo($product),
            'download_url' => $product ? route('api.v1.user.products.download', ['id' => $product->id]) : null,
            'purchase_date' => $order?->paid_at?->toIso8601String(),
            'purchase_date_formatted' => $order?->paid_at?->format('d M Y'),
        ];
    }

    protected function getFileInfo($product): ?array
    {
        if (!$product || !$product->file_url) {
            return null;
        }

        $extension = pathinfo($product->file_url, PATHINFO_EXTENSION);

        return [
            'type' => strtoupper($extension),
        ];
    }
}
