<?php

namespace App\Http\Resources\Api\V1\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource;

        return [
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'whatsapp_link' => $data['whatsapp'] ? $this->generateWhatsappLink($data['whatsapp']) : null,
            'address' => $data['address'] ?? null,
            'social_media' => $data['social_media'] ?? [],
        ];
    }

    /**
     * Generate WhatsApp link from phone number.
     *
     * @param string $phone
     * @return string
     */
    protected function generateWhatsappLink(string $phone): string
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 0, replace with 62 (Indonesia)
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return "https://wa.me/{$phone}";
    }
}
