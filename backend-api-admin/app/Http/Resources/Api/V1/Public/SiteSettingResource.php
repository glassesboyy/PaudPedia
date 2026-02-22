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
            'site_logo' => $data['site_logo'] ?? null,
            'site_favicon' => $data['site_favicon'] ?? null,

            // Hero Section
            'hero_title' => $data['hero_title'] ?? null,
            'hero_subtitle' => $data['hero_subtitle'] ?? null,
            'hero_image' => $data['hero_image'] ?? null,
            'hero_cta_text' => $data['hero_cta_text'] ?? null,
            'hero_cta_link' => $data['hero_cta_link'] ?? null,

            // Contact Information
            'contact_email' => $data['contact_email'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
            'contact_whatsapp' => $data['contact_whatsapp'] ?? null,
            'contact_address' => $data['contact_address'] ?? null,

            // Social Media
            'social_instagram' => $data['social_instagram'] ?? null,
            'social_facebook' => $data['social_facebook'] ?? null,
            'social_youtube' => $data['social_youtube'] ?? null,
            'social_linkedin' => $data['social_linkedin'] ?? null,
            'social_twitter' => $data['social_twitter'] ?? null,
            'social_tiktok' => $data['social_tiktok'] ?? null,

            // About
            'about_title' => $data['about_title'] ?? null,
            'about_description' => $data['about_description'] ?? null,
            'about_vision' => $data['about_vision'] ?? null,
            'about_mission' => $data['about_mission'] ?? null,

            // Footer
            'footer_copyright' => $data['footer_copyright'] ?? null,
            'footer_description' => $data['footer_description'] ?? null,

            // SEO
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'seo_keywords' => $data['seo_keywords'] ?? null,
        ];
    }
}
