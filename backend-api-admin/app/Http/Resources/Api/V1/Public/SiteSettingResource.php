<?php

namespace App\Http\Resources\Api\V1\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteSettingResource extends JsonResource
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
            // Branding
            'site_name' => $data['site_name'] ?? 'PaudPedia',
            'site_tagline' => $data['site_tagline'] ?? null,
            'site_description' => $data['site_description'] ?? null,

            // Contact Information
            'contact_email' => $data['contact_email'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
            'contact_address' => $data['contact_address'] ?? null,

            // Social Media
            'social_facebook' => $data['social_facebook'] ?? null,
            'social_instagram' => $data['social_instagram'] ?? null,
            'social_twitter' => $data['social_twitter'] ?? null,
            'social_youtube' => $data['social_youtube'] ?? null,
            'social_tiktok' => $data['social_tiktok'] ?? null,
            'social_linkedin' => $data['social_linkedin'] ?? null,
            'social_telegram' => $data['social_telegram'] ?? null,
            'social_discord' => $data['social_discord'] ?? null,
        ];
    }
}
