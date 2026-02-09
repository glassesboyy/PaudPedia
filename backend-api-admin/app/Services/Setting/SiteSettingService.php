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
     * Seed default settings
     *
     * @return void
     * @throws \Exception
     */
    public function seedDefaults(): void
    {
        $defaults = [
            // Identitas Situs
            'site_name' => ['value' => 'PAUD Pedia', 'type' => 'string', 'description' => 'Nama situs'],
            'site_tagline' => ['value' => 'Platform Edukasi PAUD Terpadu', 'type' => 'string', 'description' => 'Tagline situs'],
            'site_logo' => ['value' => null, 'type' => 'string', 'description' => 'Path logo situs'],
            'site_favicon' => ['value' => null, 'type' => 'string', 'description' => 'Path favicon'],

            // Informasi Kontak
            'contact_email' => ['value' => 'info@paudpedia.com', 'type' => 'string', 'description' => 'Email kontak'],
            'contact_phone' => ['value' => null, 'type' => 'string', 'description' => 'Nomor telepon'],
            'contact_whatsapp' => ['value' => null, 'type' => 'string', 'description' => 'Nomor WhatsApp'],
            'contact_address' => ['value' => null, 'type' => 'string', 'description' => 'Alamat lengkap'],

            // Media Sosial
            'social_facebook' => ['value' => null, 'type' => 'string', 'description' => 'Link Facebook'],
            'social_instagram' => ['value' => null, 'type' => 'string', 'description' => 'Link Instagram'],
            'social_twitter' => ['value' => null, 'type' => 'string', 'description' => 'Link Twitter/X'],
            'social_youtube' => ['value' => null, 'type' => 'string', 'description' => 'Link YouTube'],
            'social_linkedin' => ['value' => null, 'type' => 'string', 'description' => 'Link LinkedIn'],
            'social_tiktok' => ['value' => null, 'type' => 'string', 'description' => 'Link TikTok'],

            // SEO
            'meta_description' => ['value' => null, 'type' => 'string', 'description' => 'Meta description untuk SEO'],
            'meta_keywords' => ['value' => null, 'type' => 'string', 'description' => 'Meta keywords untuk SEO'],

            // Integrasi
            'google_analytics_id' => ['value' => null, 'type' => 'string', 'description' => 'Google Analytics ID'],
            'google_tag_manager' => ['value' => null, 'type' => 'string', 'description' => 'Google Tag Manager script'],
            'facebook_pixel' => ['value' => null, 'type' => 'string', 'description' => 'Facebook Pixel script'],

            // Footer
            'footer_about' => ['value' => null, 'type' => 'string', 'description' => 'Tentang kami di footer'],
            'footer_copyright' => ['value' => '© 2026 PAUD Pedia. All rights reserved.', 'type' => 'string', 'description' => 'Teks copyright'],
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
