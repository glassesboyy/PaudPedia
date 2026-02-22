<?php

namespace App\Http\Resources\Api\V1\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'type' => $this->type?->value,
            'items_count' => $this->whenCounted('courses', $this->courses_count)
                ?? $this->whenCounted('products', $this->products_count)
                ?? $this->whenCounted('articles', $this->articles_count)
                ?? null,
        ];
    }
}
