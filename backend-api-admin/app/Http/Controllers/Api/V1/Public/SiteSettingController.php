<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\Public\SiteSettingResource;
use App\Services\Setting\SiteSettingService;
use Illuminate\Http\JsonResponse;

class SiteSettingController extends BaseController
{
    public function __construct(
        protected SiteSettingService $siteSettingService
    ) {}

    /**
     * Get all public site settings.
     * 
     * @unauthenticated
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $settings = $this->siteSettingService->getPublicSettings();

        return $this->success(
            new SiteSettingResource($settings),
            'Pengaturan situs berhasil dimuat'
        );
    }

    /**
     * Get specific site setting by key.
     * 
     * @unauthenticated
     * @param string $key
     * @return JsonResponse
     */
    public function show(string $key): JsonResponse
    {
        $value = $this->siteSettingService->getPublicSetting($key);

        if ($value === null) {
            return $this->notFound('Pengaturan tidak ditemukan');
        }

        return $this->success(
            ['key' => $key, 'value' => $value],
            'Pengaturan berhasil dimuat'
        );
    }
}
