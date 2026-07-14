<?php

namespace App\Http\Resources\Api\V1\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebinarResource extends JsonResource
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
            $isOwned = \App\Models\OrderItem::where('item_type', \App\Enums\OrderItemType::WEBINAR->value)
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
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'scheduled_date' => $this->scheduled_at?->format('d M Y'),
            'scheduled_time' => $this->scheduled_at?->format('H:i'),
            'duration_minutes' => $this->duration_minutes,
            'max_participants' => $this->max_participants,
            'total_purchases' => $this->total_purchases,
            'is_full' => $this->max_participants ? ($this->total_purchases >= $this->max_participants) : false,
            'is_upcoming' => $this->isUpcoming(),
            'mentor' => $this->whenLoaded('mentor', function () {
                return [
                    'id' => $this->mentor->id,
                    'name' => $this->mentor->name,
                    'title' => $this->mentor->title,
                    'photo_url' => $this->mentor->photo_url ? asset('storage/' . $this->mentor->photo_url) : null,
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
     * Check if webinar has discount.
     *
     * @return bool
     */
    protected function hasDiscount(): bool
    {
        return $this->original_price && $this->original_price > $this->price;
    }
}
