<?php

namespace App\Filament\Resources\Schools\Schemas;

use App\Enums\SubscriptionPlan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SchoolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
            Section::make('Informasi Sekolah')
                ->description('Data dasar sekolah')
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Sekolah')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1),

                    TextInput::make('npsn')
                        ->label('NPSN')
                        ->helperText('Nomor Pokok Sekolah Nasional')
                        ->maxLength(8)
                        ->unique(ignoreRecord: true)
                        ->columnSpan(1),

                    TextInput::make('phone')
                        ->label('Nomor Telepon')
                        ->numeric()
                        ->maxLength(20)
                        ->placeholder('Contoh: 0271123456')
                        ->helperText('Hanya angka, tanpa spasi atau karakter khusus')
                        ->columnSpan(1),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(255)
                        ->columnSpan(1),

                    Textarea::make('address')
                        ->label('Alamat')
                        ->rows(3)
                        ->maxLength(500)
                        ->columnSpanFull(),

                    FileUpload::make('logo_url')
                        ->label('Logo Sekolah')
                        ->image()
                        ->imageEditor()
                        ->imageAspectRatio([
                            '1:1',
                        ])
                        ->automaticallyCropImagesToAspectRatio()
                        ->disk('public')
                        ->directory('school-logos')
                        ->visibility('public')
                        ->maxSize(2048)
                        ->helperText('Format: JPG, PNG. Maksimal 2MB. Rasio 1:1 (kotak)')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Pengaturan Subscription')
                ->description('Kelola paket langganan dan batasan sekolah')
                ->schema([
                    Select::make('subscription_plan')
                        ->label('Paket Langganan')
                        ->options(SubscriptionPlan::class)
                        ->required()
                        ->native(false)
                        ->helperText('FREE: 20 siswa, 5 guru | PRO: Unlimited')
                        ->columnSpan(1),

                    DatePicker::make('subscription_ended_at')
                        ->label('Tanggal Kadaluarsa')
                        ->native(false)
                        ->minDate(now())
                        ->displayFormat('d/m/Y')
                        ->helperText('Kosongkan untuk langganan tanpa batas waktu')
                        ->columnSpan(1),
                ])
                ->columns(2),

            Section::make('Status')
                ->description('Status aktivasi sekolah')
                ->schema([
                    Toggle::make('is_active')
                        ->label('Status Aktif')
                        ->default(true)
                        ->helperText('Nonaktifkan untuk melarang akses sekolah ini'),
                ]),
            ]);
    }
}
