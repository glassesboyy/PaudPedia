<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SiteSettingResource;
use App\Services\Setting\SiteSettingService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;

class ManageSiteSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string $resource = SiteSettingResource::class;

    protected static ?string $title = 'Pengaturan Situs';

    public ?array $data = [];

    protected SiteSettingService $siteSettingService;

    public function getView(): string
    {
        return 'filament.resources.settings.pages.manage-site-settings';
    }

    public function boot(SiteSettingService $siteSettingService): void
    {
        $this->siteSettingService = $siteSettingService;
    }

    public function mount(): void
    {
        $this->schema->fill($this->siteSettingService->getAllSettings());
    }

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->schema($this->getFormSchema())
            ->statePath('data');
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Informasi Situs')
                ->description('Identitas dan deskripsi website')
                ->schema([
                    TextInput::make('site_name')
                        ->label('Nama Situs')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Contoh: PaudPedia')
                        ->helperText('Nama website yang akan ditampilkan'),

                    TextInput::make('site_tagline')
                        ->label('Tagline')
                        ->maxLength(255)
                        ->placeholder('Contoh: Platform Pendidikan Anak Usia Dini Terpadu')
                        ->helperText('Tagline atau slogan website'),

                    Textarea::make('site_description')
                        ->label('Deskripsi Situs')
                        ->rows(3)
                        ->maxLength(500)
                        ->placeholder('Deskripsi singkat tentang platform')
                        ->helperText('Deskripsi website untuk SEO dan informasi umum'),
                ])
                ->columns(1)
                ->collapsible(),

            Section::make('Kontak')
                ->description('Informasi kontak dan alamat')
                ->schema([
                    TextInput::make('contact_email')
                        ->label('Email Kontak')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Contoh: info@paudpedia.com')
                        ->helperText('Email untuk kontak umum'),

                    TextInput::make('contact_phone')
                        ->label('Nomor Telepon')
                        ->tel()
                        ->maxLength(20)
                        ->placeholder('Contoh: 021-12345678')
                        ->helperText('Nomor telepon yang bisa dihubungi'),

                    Textarea::make('contact_address')
                        ->label('Alamat')
                        ->rows(3)
                        ->maxLength(500)
                        ->placeholder('Contoh: Jl. Pendidikan No. 123, Jakarta')
                        ->helperText('Alamat lengkap kantor'),
                ])
                ->columns(1)
                ->collapsible(),

            Section::make('Media Sosial')
                ->description('Link profil media sosial')
                ->schema([
                    TextInput::make('social_facebook')
                        ->label('Facebook')
                        ->url()
                        ->maxLength(255)
                        ->placeholder('Contoh: https://facebook.com/paudpedia')
                        ->helperText('Link profil Facebook')
                        ->prefixIcon('heroicon-m-globe-alt'),

                    TextInput::make('social_instagram')
                        ->label('Instagram')
                        ->url()
                        ->maxLength(255)
                        ->placeholder('Contoh: https://instagram.com/paudpedia')
                        ->helperText('Link profil Instagram')
                        ->prefixIcon('heroicon-m-globe-alt'),

                    TextInput::make('social_twitter')
                        ->label('Twitter / X')
                        ->url()
                        ->maxLength(255)
                        ->placeholder('Contoh: https://twitter.com/paudpedia')
                        ->helperText('Link profil Twitter / X')
                        ->prefixIcon('heroicon-m-globe-alt'),

                    TextInput::make('social_youtube')
                        ->label('YouTube')
                        ->url()
                        ->maxLength(255)
                        ->placeholder('Contoh: https://youtube.com/@paudpedia')
                        ->helperText('Link channel YouTube')
                        ->prefixIcon('heroicon-m-globe-alt'),
                ])
                ->columns(1)
                ->collapsible(),

            Section::make('Pengaturan Pembayaran (Midtrans)')
                ->description('Konfigurasi payment gateway Midtrans')
                ->schema([
                    TextInput::make('midtrans_client_key')
                        ->label('Client Key')
                        ->maxLength(255)
                        ->placeholder('SB-Mid-client-xxx atau Mid-client-xxx')
                        ->helperText('Midtrans Client Key untuk frontend'),

                    TextInput::make('midtrans_server_key')
                        ->label('Server Key')
                        ->password()
                        ->maxLength(255)
                        ->placeholder('SB-Mid-server-xxx atau Mid-server-xxx')
                        ->helperText('Midtrans Server Key untuk backend (disembunyikan)'),

                    Toggle::make('midtrans_is_production')
                        ->label('Mode Production')
                        ->helperText('Aktifkan untuk menggunakan production environment Midtrans')
                        ->default(false),
                ])
                ->columns(1)
                ->collapsible()
                ->collapsed(),

            Section::make('Pengaturan Langganan')
                ->description('Konfigurasi paket gratis dan pro untuk sekolah')
                ->schema([
                    TextInput::make('free_max_students')
                        ->label('Maks. Siswa (Paket Gratis)')
                        ->numeric()
                        ->minValue(0)
                        ->default(20)
                        ->helperText('Jumlah maksimal siswa untuk paket gratis'),

                    TextInput::make('free_max_teachers')
                        ->label('Maks. Guru (Paket Gratis)')
                        ->numeric()
                        ->minValue(0)
                        ->default(5)
                        ->helperText('Jumlah maksimal guru untuk paket gratis'),

                    TextInput::make('pro_monthly_price')
                        ->label('Harga Pro (Bulanan)')
                        ->numeric()
                        ->prefix('Rp')
                        ->minValue(0)
                        ->default(100000)
                        ->helperText('Harga langganan Pro per bulan'),
                ])
                ->columns(2)
                ->collapsible(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Pengaturan')
                ->action('save')
                ->icon('heroicon-m-check')
        ];
    }

    public function getCachedFormActions(): array
    {
        return $this->getFormActions();
    }

    public function save(): void
    {
        $data = $this->schema->getState();

        $this->siteSettingService->saveSettings($data);

        Notification::make()
            ->success()
            ->title('Pengaturan Berhasil Disimpan')
            ->body('Pengaturan situs telah diperbarui.')
            ->send();
    }
}
