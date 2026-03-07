<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\Public\ContactInfoResource;
use App\Services\Setting\SiteSettingService;
use Illuminate\Http\JsonResponse;

class ContactController extends BaseController
{
    public function __construct(
        protected SiteSettingService $siteSettingService
    ) {}

    /**
     * Get contact information.
     *
     * @unauthenticated
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $settings = $this->siteSettingService->getPublicSettings();

        $contactInfo = [
            'email' => $settings['contact_email'] ?? null,
            'phone' => $settings['contact_phone'] ?? null,
            'address' => $settings['contact_address'] ?? null,
            'social_media' => [
                'facebook' => $settings['social_facebook'] ?? null,
                'instagram' => $settings['social_instagram'] ?? null,
                'twitter' => $settings['social_twitter'] ?? null,
                'youtube' => $settings['social_youtube'] ?? null,
                'tiktok' => $settings['social_tiktok'] ?? null,
                'linkedin' => $settings['social_linkedin'] ?? null,
                'telegram' => $settings['social_telegram'] ?? null,
                'discord' => $settings['social_discord'] ?? null,
            ],
        ];

        // Remove null values from social media
        $contactInfo['social_media'] = array_filter($contactInfo['social_media']);

        return $this->success(
            new ContactInfoResource($contactInfo),
            'Informasi kontak berhasil dimuat'
        );
    }
}
