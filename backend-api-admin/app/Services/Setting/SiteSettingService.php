<?php

namespace App\Services\Setting;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SiteSettingService
{
    /**
     * Get all settings as key-value array
     *
     * @return array
     */
    public function getAllSettings(): array
    {
        return Cache::remember('site_settings_all', 3600, function () {
            $settings = SiteSetting::all();
            $data = [];

            foreach ($settings as $setting) {
                $data[$setting->key] = $setting->casted_value;
            }

            return $data;
        });
    }

    /**
     * Get specific setting by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return Cache::remember("site_setting_{$key}", 3600, function () use ($key, $default) {
            return SiteSetting::get($key, $default);
        });
    }

    /**
     * Save multiple settings at once
     *
     * @param array $data
     * @return void
     * @throws \Exception
     */
    public function saveSettings(array $data): void
    {
        DB::beginTransaction();
        try {
            foreach ($data as $key => $value) {
                // Determine type based on value
                $type = $this->determineType($value);

                SiteSetting::set($key, $value, $type);
            }

            DB::commit();

            // Clear cache after successful save
            $this->clearCache();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Save single setting
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $type
     * @param string|null $description
     * @return void
     * @throws \Exception
     */
    public function saveSetting(string $key, mixed $value, ?string $type = null, ?string $description = null): void
    {
        DB::beginTransaction();
        try {
            $type = $type ?? $this->determineType($value);

            $settingData = [
                'value' => SiteSetting::prepareValue($value, $type),
                'type' => $type,
            ];

            if ($description) {
                $settingData['description'] = $description;
            }

            SiteSetting::updateOrCreate(
                ['key' => $key],
                $settingData
            );

            DB::commit();

            // Clear specific cache
            Cache::forget("site_setting_{$key}");
            Cache::forget('site_settings_all');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete setting by key
     *
     * @param string $key
     * @return bool
     * @throws \Exception
     */
    public function deleteSetting(string $key): bool
    {
        DB::beginTransaction();
        try {
            $deleted = SiteSetting::where('key', $key)->delete();

            DB::commit();

            if ($deleted) {
                Cache::forget("site_setting_{$key}");
                Cache::forget('site_settings_all');
            }

            return (bool) $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Clear all settings cache
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget('site_settings_all');

        // Clear individual setting caches
        $settings = SiteSetting::all();
        foreach ($settings as $setting) {
            Cache::forget("site_setting_{$setting->key}");
        }
    }

    /**
     * Determine data type automatically
     *
     * @param mixed $value
     * @return string
     */
    protected function determineType(mixed $value): string
    {
        return match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_float($value) => 'float',
            is_array($value) => 'json',
            default => 'string',
        };
    }

    /**
     * Public keys that are safe to expose via API
     *
     * @return array
     */
    protected function getPublicKeys(): array
    {
        return [
            // Branding
            'site_name',
            'site_tagline',
            'site_description',

            // Contact Information
            'contact_email',
            'contact_phone',
            'contact_address',

            // Social Media
            'social_facebook',
            'social_instagram',
            'social_twitter',
            'social_youtube',
            'social_tiktok',
            'social_linkedin',
            'social_telegram',
            'social_discord',
        ];
    }

    /**
     * Get all public site settings (safe to expose via API)
     *
     * @return array
     */
    public function getPublicSettings(): array
    {
        $allSettings = $this->getAllSettings();
        $publicKeys = $this->getPublicKeys();

        return array_filter(
            $allSettings,
            fn($key) => in_array($key, $publicKeys),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Get specific public site setting by key
     *
     * @param string $key
     * @return mixed
     */
    public function getPublicSetting(string $key): mixed
    {
        $publicKeys = $this->getPublicKeys();

        if (!in_array($key, $publicKeys)) {
            return null;
        }

        return $this->getSetting($key);
    }

    /**
     * Seed default settings
     *
     * @return void
     * @throws \Exception
     */
    public function seedDefaults(): void
    {
        $defaults = [
            // Identitas Situs
            'site_name' => ['value' => 'PaudPedia', 'type' => 'string', 'description' => 'Nama situs'],
            'site_tagline' => ['value' => 'Platform Pendidikan Anak Usia Dini Terpadu', 'type' => 'string', 'description' => 'Tagline situs'],
            'site_description' => ['value' => 'Platform e-learning dan marketplace untuk pendidikan anak usia dini (PAUD).', 'type' => 'string', 'description' => 'Deskripsi situs'],

            // Informasi Kontak
            'contact_email' => ['value' => 'info@paudpedia.com', 'type' => 'string', 'description' => 'Email kontak'],
            'contact_phone' => ['value' => null, 'type' => 'string', 'description' => 'Nomor telepon'],
            'contact_address' => ['value' => null, 'type' => 'string', 'description' => 'Alamat lengkap'],

            // Media Sosial
            'social_facebook' => ['value' => null, 'type' => 'string', 'description' => 'Link Facebook'],
            'social_instagram' => ['value' => null, 'type' => 'string', 'description' => 'Link Instagram'],
            'social_twitter' => ['value' => null, 'type' => 'string', 'description' => 'Link Twitter/X'],
            'social_youtube' => ['value' => null, 'type' => 'string', 'description' => 'Link YouTube'],
            'social_tiktok' => ['value' => null, 'type' => 'string', 'description' => 'Link TikTok'],
            'social_linkedin' => ['value' => null, 'type' => 'string', 'description' => 'Link LinkedIn'],
            'social_telegram' => ['value' => null, 'type' => 'string', 'description' => 'Link Telegram'],
            'social_discord' => ['value' => null, 'type' => 'string', 'description' => 'Link Discord'],

            // Integrasi (private, not exposed via public API)
            'midtrans_client_key' => ['value' => null, 'type' => 'string', 'description' => 'Midtrans Client Key'],
            'midtrans_server_key' => ['value' => null, 'type' => 'string', 'description' => 'Midtrans Server Key'],
            'midtrans_is_production' => ['value' => false, 'type' => 'boolean', 'description' => 'Midtrans Production Mode'],

            // Paket Langganan (private, not exposed via public API)
            'free_max_students' => ['value' => 30, 'type' => 'integer', 'description' => 'Maks siswa paket gratis'],
            'free_max_teachers' => ['value' => 5, 'type' => 'integer', 'description' => 'Maks guru paket gratis'],
            'pro_monthly_price' => ['value' => 150000, 'type' => 'integer', 'description' => 'Harga langganan pro per bulan'],
        ];

        DB::beginTransaction();
        try {
            foreach ($defaults as $key => $config) {
                SiteSetting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => $config['value'],
                        'type' => $config['type'],
                        'description' => $config['description'],
                    ]
                );
            }

            DB::commit();
            $this->clearCache();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
