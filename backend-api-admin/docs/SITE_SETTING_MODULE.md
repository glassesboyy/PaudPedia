# SITE SETTING MODULE - Admin Panel Documentation

## 📋 Overview

Modul **Site Settings** adalah admin panel untuk mengelola pengaturan situs secara terpusat. Module ini menggunakan pattern **singleton** (satu halaman edit) bukan CRUD standar.

**Use Case:** UC-A-04 - Site settings (Edit logo, kontak, social media platform)  
**Functional Requirement:** FR-AD-02 - Sistem harus dapat mengelola pengaturan situs

## 🏗️ Arsitektur

Mengikuti **BACKEND_ARSITEKTUR.md** dengan struktur:

```
app/
├── Filament/
│   └── Resources/
│       └── Settings/
│           ├── SiteSettingResource.php         # Resource utama
│           └── Pages/
│               └── ManageSiteSettings.php      # Custom page (singleton)
├── Services/
│   └── Setting/
│       └── SiteSettingService.php              # Business logic
├── Policies/
│   └── SiteSettingPolicy.php                   # Authorization
└── Models/
    └── SiteSetting.php                         # Model (sudah ada)

database/
└── seeders/
    └── SiteSettingSeeder.php                   # Default settings

resources/
└── views/
    └── filament/
        └── resources/
            └── settings/
                └── pages/
                    └── manage-site-settings.blade.php  # Custom view
```

## 📦 Files Created

### 1. **SiteSettingResource.php**
- **Location:** `app/Filament/Resources/Settings/SiteSettingResource.php`
- **Purpose:** Main Filament resource (singleton pattern)
- **Key Features:**
  - Navigation group: "Pengaturan"
  - Icon: `heroicon-o-cog-6-tooth`
  - Disable create/delete (singleton)
  - Only one page: `ManageSiteSettings`

### 2. **ManageSiteSettings.php** (Custom Page)
- **Location:** `app/Filament/Resources/Settings/Pages/ManageSiteSettings.php`
- **Purpose:** Single page untuk edit semua settings
- **Key Features:**
  - Load settings dari `SiteSettingService::getAllSettings()`
  - Form dengan 7 sections (collapsible):
    1. **Identitas Situs** - Logo, favicon, nama, tagline
    2. **Informasi Kontak** - Email, phone, WhatsApp, address
    3. **Media Sosial** - Facebook, Instagram, Twitter, YouTube, LinkedIn, TikTok
    4. **SEO & Meta Tags** - Description, keywords
    5. **Integrasi Pihak Ketiga** - Google Analytics, GTM, Facebook Pixel
    6. **Pengaturan Footer** - About text, copyright
  - Save action dengan confirmation
  - Notification Bahasa Indonesia

### 3. **SiteSettingService.php**
- **Location:** `app/Filament/Resources/Services/Setting/SiteSettingService.php`
- **Purpose:** Fat service layer dengan business logic
- **Methods:**
  - `getAllSettings(): array` - Get all settings (cached)
  - `getSetting(string $key, mixed $default): mixed` - Get single setting
  - `saveSettings(array $data): void` - Save multiple settings (DB transaction)
  - `saveSetting(...): void` - Save single setting
  - `deleteSetting(string $key): bool` - Delete setting
  - `clearCache(): void` - Clear cache
  - `seedDefaults(): void` - Seed default settings

### 4. **SiteSettingPolicy.php**
- **Location:** `app/Policies/SiteSettingPolicy.php`
- **Purpose:** Authorization
- **Access:** **Admin only** (no moderator access)
- **Methods:** viewAny, view, create, update, delete, deleteAny, restore, forceDelete

### 5. **manage-site-settings.blade.php**
- **Location:** `resources/views/filament/resources/settings/pages/manage-site-settings.blade.php`
- **Purpose:** Custom Blade view untuk halaman
- **Content:** Simple Filament page with form

## 🗃️ Database Structure

Tabel `site_settings` (sudah ada):

```sql
CREATE TABLE site_settings (
    id BIGINT PRIMARY KEY,
    key VARCHAR(255) UNIQUE,      -- ex: "site_logo"
    value TEXT,                    -- JSON for complex values
    type VARCHAR(255) DEFAULT 'string',  -- string, json, boolean, integer
    description VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX (key)
);
```

## 📝 Settings Keys

### Identitas Situs
- `site_name` (string) - Nama situs
- `site_tagline` (string) - Tagline situs
- `site_logo` (string) - Path logo situs
- `site_favicon` (string) - Path favicon

### Informasi Kontak
- `contact_email` (string) - Email kontak
- `contact_phone` (string) - Nomor telepon
- `contact_whatsapp` (string) - Nomor WhatsApp
- `contact_address` (string) - Alamat lengkap

### Media Sosial
- `social_facebook` (string) - Link Facebook
- `social_instagram` (string) - Link Instagram
- `social_twitter` (string) - Link Twitter/X
- `social_youtube` (string) - Link YouTube
- `social_linkedin` (string) - Link LinkedIn
- `social_tiktok` (string) - Link TikTok

### SEO & Meta Tags
- `meta_description` (string) - Meta description
- `meta_keywords` (string) - Meta keywords

### Integrasi Pihak Ketiga
- `google_analytics_id` (string) - Google Analytics ID
- `google_tag_manager` (string) - GTM script
- `facebook_pixel` (string) - Facebook Pixel script

### Footer
- `footer_about` (string) - About text di footer
- `footer_copyright` (string) - Copyright text

## 🔐 Authorization

**Access Level:** **Admin Only**

```php
// SiteSettingPolicy.php
public function viewAny(User $user): bool
{
    return $user->hasRole('admin');  // ❌ Moderator tidak bisa akses
}
```

**Alasan:** Settings situs adalah konfigurasi critical yang hanya boleh diubah oleh Super Admin.

## 🎨 UI/UX Features

### Form Layout
- **Pattern:** 1 kolom vertikal per section (columns(1))
- **Sections:** 7 sections, semua collapsible
- **Helper Text:** Setiap field ada helper text Bahasa Indonesia
- **Placeholders:** Contoh value untuk guidance
- **File Uploads:** Image editor untuk logo/favicon
- **Validation:** Required untuk fields critical (site_name, contact_email)

### Save Button
- **Label:** "Simpan Pengaturan"
- **Confirmation:** Modal "Simpan Pengaturan?"
- **Description:** "Pengaturan situs akan diperbarui dan langsung diterapkan."
- **Notification:** "Pengaturan Berhasil Disimpan"

## 🚀 Usage

### Admin Panel
1. Login sebagai Admin
2. Sidebar → **Pengaturan** → **Pengaturan Situs**
3. Edit fields yang diperlukan
4. Klik **Simpan Pengaturan**
5. Confirm → Settings saved & cache cleared

### Frontend/API (Get Settings)

```php
use App\Services\Setting\SiteSettingService;

$service = app(SiteSettingService::class);

// Get all settings
$settings = $service->getAllSettings();
// ['site_name' => 'PAUD Pedia', 'contact_email' => '...', ...]

// Get single setting
$siteName = $service->getSetting('site_name', 'Default Name');
$logo = $service->getSetting('site_logo');
```

### Direct Model Access

```php
use App\Models\SiteSetting;

// Get setting
$siteName = SiteSetting::get('site_name', 'Default');

// Set setting
SiteSetting::set('site_name', 'New Name', 'string');
```

## 🔄 Caching Strategy

**Cache Duration:** 3600 seconds (1 hour)

**Cache Keys:**
- `site_settings_all` - All settings
- `site_setting_{key}` - Individual setting

**Auto Clear:** Cache auto-cleared saat save settings.

**Manual Clear:**
```php
$service->clearCache();
```

## 🧪 Testing

### Manual Testing
1. ✅ Login sebagai Admin → Bisa akses menu
2. ✅ Login sebagai Moderator → Tidak bisa akses menu
3. ✅ Upload logo → File tersimpan di `storage/app/public/site-settings/logo/`
4. ✅ Upload favicon → File tersimpan di `storage/app/public/site-settings/favicon/`
5. ✅ Edit site_name → Saved & cache cleared
6. ✅ Edit contact_email (invalid) → Validation error
7. ✅ Edit social media links (invalid URL) → Validation error
8. ✅ Save settings → Notification success muncul
9. ✅ Refresh page → Data tetap tersimpan
10. ✅ Call `getSetting()` dari frontend → Return value correct

### Validation Testing
- ✅ `site_name` - Required, max 255
- ✅ `contact_email` - Required, email format, max 255
- ✅ `social_*` - URL format, max 255
- ✅ `meta_description` - Max 160 characters
- ✅ `site_logo` - Image, max 2MB
- ✅ `site_favicon` - Image, max 512KB

## 📊 Performance

- **Cache:** All settings di-cache 1 jam
- **DB Queries:** 1 query untuk load all settings (cached)
- **Transaction:** Save menggunakan `DB::transaction()`
- **N+1 Prevention:** Tidak ada N+1 query (single settings table)

## 🔧 Maintenance

### Add New Setting
1. Edit `SiteSettingService::seedDefaults()` - Add new key
2. Edit `ManageSiteSettings::schema()` - Add form field
3. Run seeder: `php artisan db:seed --class=SiteSettingSeeder`

### Update Setting Value via Code
```php
$service = app(SiteSettingService::class);
$service->saveSetting('new_key', 'new_value', 'string', 'Description');
```

## 📚 References

- **Arsitektur:** BACKEND_ARSITEKTUR.md
- **Use Case:** USE_CASE.md (UC-A-04)
- **Migration:** `database/migrations/2026_01_17_000005_create_site_settings_table.php`
- **Model:** `app/Models/SiteSetting.php`
- **Seeder:** `database/seeders/SiteSettingSeeder.php`

## ✅ Checklist Implementation

- [x] SiteSettingResource.php (singleton pattern)
- [x] ManageSiteSettings.php (custom page)
- [x] SiteSettingService.php (business logic)
- [x] SiteSettingPolicy.php (admin only)
- [x] manage-site-settings.blade.php (view)
- [x] Register policy di AuthServiceProvider
- [x] Bahasa Indonesia UI (labels, notifications)
- [x] Validation rules
- [x] Cache strategy
- [x] DB transactions
- [x] Helper texts & placeholders
- [x] File upload (logo, favicon)
- [x] Collapsible sections
- [x] Confirmation modal

## 🎉 Summary

Module **Site Settings** sepenuhnya mengikuti arsitektur yang didefinisikan:
- ✅ Pattern singleton (1 halaman edit)
- ✅ Fat service layer (business logic di service)
- ✅ Policy-based authorization (Admin only)
- ✅ Cache strategy (1 jam)
- ✅ DB transactions
- ✅ Bahasa Indonesia UI
- ✅ Validation comprehensive
- ✅ Single-column vertical layout
- ✅ Collapsible sections
- ✅ Helper texts

**Access:** Admin Panel → Pengaturan → Pengaturan Situs
