<?php

namespace App\Http\Resources\Api\V1\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user('sanctum');
        $isOwned = false;

        if ($user) {
            $isOwned = \App\Models\OrderItem::where('item_type', \App\Enums\OrderItemType::PRODUCT->value)
                ->where('item_id', $this->id)
                ->whereHas('order', function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->where('status', \App\Enums\OrderStatus::PAID->value);
                })
                ->exists();
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->getExcerpt(),
            'thumbnail_url' => $this->thumbnail_url ? asset('storage/' . $this->thumbnail_url) : null,
            'is_owned' => $isOwned,
            'price' => (float) $this->price,
            'original_price' => $this->original_price ? (float) $this->original_price : null,
            'has_discount' => $this->hasDiscount(),
            'discount_percentage' => $this->discount_percentage,
            'file_info' => $this->getFileInfo(),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    /**
     * Get excerpt from description.
     *
     * @return string|null
     */
    protected function getExcerpt(): ?string
    {
        if (!$this->description) {
            return null;
        }

        $plainText = strip_tags($this->description);
        return strlen($plainText) > 150 ? substr($plainText, 0, 150) . '...' : $plainText;
    }

    /**
     * Check if product has discount.
     *
     * @return bool
     */
    protected function hasDiscount(): bool
    {
        return $this->original_price && $this->original_price > $this->price;
    }

    /**
     * Get file info (type and size).
     *
     * @return array|null
     */
    protected function getFileInfo(): ?array
    {
        if (!$this->file_url) {
            return null;
        }

        $extension = pathinfo($this->file_url, PATHINFO_EXTENSION);
        $size = null;

        if (Storage::disk('local')->exists($this->file_url)) {
            $size = Storage::disk('local')->size($this->file_url);
        }

        return [
            'type' => strtoupper($extension),
            'size' => $size,
            'size_formatted' => $size ? $this->formatBytes($size) : null,
        ];
    }

    /**
     * Format bytes to human readable format.
     *
     * @param int $bytes
     * @return string
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
