<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'site_name', 'value' => 'PaudPedia', 'type' => 'string', 'description' => 'Nama website'],
            ['key' => 'site_tagline', 'value' => 'Platform Pendidikan Anak Usia Dini Terpadu', 'type' => 'string', 'description' => 'Tagline website'],
            ['key' => 'site_description', 'value' => 'PaudPedia adalah platform terpadu untuk manajemen sekolah PAUD dan pengembangan profesional guru', 'type' => 'string', 'description' => 'Deskripsi website'],
            ['key' => 'contact_email', 'value' => 'info@paudpedia.com', 'type' => 'string', 'description' => 'Email kontak'],
            ['key' => 'contact_phone', 'value' => '021-12345678', 'type' => 'string', 'description' => 'Nomor telepon kontak'],
            ['key' => 'contact_address', 'value' => 'Jl. Pendidikan No. 123, Jakarta', 'type' => 'string', 'description' => 'Alamat kantor'],

            // Social Media
            ['key' => 'social_facebook', 'value' => 'https://facebook.com/paudpedia', 'type' => 'string', 'description' => 'Facebook URL'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/paudpedia', 'type' => 'string', 'description' => 'Instagram URL'],
            ['key' => 'social_twitter', 'value' => 'https://twitter.com/paudpedia', 'type' => 'string', 'description' => 'Twitter URL'],
            ['key' => 'social_youtube', 'value' => 'https://youtube.com/@paudpedia', 'type' => 'string', 'description' => 'YouTube URL'],

            // Payment Settings
            ['key' => 'midtrans_client_key', 'value' => 'SB-Mid-client-xxx', 'type' => 'string', 'description' => 'Midtrans Client Key'],
            ['key' => 'midtrans_server_key', 'value' => 'SB-Mid-server-xxx', 'type' => 'string', 'description' => 'Midtrans Server Key'],
            ['key' => 'midtrans_is_production', 'value' => '0', 'type' => 'boolean', 'description' => 'Midtrans Production Mode'],

            // Feature Flags
            ['key' => 'feature_courses', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable Courses'],
            ['key' => 'feature_webinars', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable Webinars'],
            ['key' => 'feature_products', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable Products'],
            ['key' => 'feature_articles', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable Articles'],

            // Subscription Settings
            ['key' => 'free_max_students', 'value' => '20', 'type' => 'integer', 'description' => 'Max students for free plan'],
            ['key' => 'free_max_teachers', 'value' => '5', 'type' => 'integer', 'description' => 'Max teachers for free plan'],
            ['key' => 'pro_monthly_price', 'value' => '100000', 'type' => 'decimal', 'description' => 'Pro plan monthly price'],
            ['key' => 'pro_yearly_price', 'value' => '1000000', 'type' => 'decimal', 'description' => 'Pro plan yearly price'],

            // Other Settings
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'description' => 'Maintenance mode'],
            ['key' => 'registration_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable user registration'],
            ['key' => 'default_timezone', 'value' => 'Asia/Jakarta', 'type' => 'string', 'description' => 'Default timezone'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::create($setting);
        }
    }
}
