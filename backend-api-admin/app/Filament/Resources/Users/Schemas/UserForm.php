<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\Gender;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
            Section::make('Informasi Pengguna')
                ->description('Data dasar pengguna')
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->columnSpan(1),

                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->revealable()
                        ->required(fn (string $context): bool => $context === 'create')
                        ->dehydrated(fn ($state) => filled($state))
                        ->minLength(8)
                        ->maxLength(255)
                        ->helperText('Minimal 8 karakter. Kosongkan jika tidak ingin mengubah password.')
                        ->columnSpan(1),

                    TextInput::make('phone')
                        ->label('Nomor Telepon')
                        ->tel()
                        ->maxLength(20)
                        ->columnSpan(1),
                ])
                ->columns(2),

            Section::make('Informasi Pribadi')
                ->description('Data pribadi pengguna')
                ->schema([
                    Select::make('gender')
                        ->label('Jenis Kelamin')
                        ->options(Gender::class)
                        ->native(false)
                        ->columnSpan(1),

                    DatePicker::make('date_of_birth')
                        ->label('Tanggal Lahir')
                        ->native(false)
                        ->maxDate(now())
                        ->displayFormat('d/m/Y')
                        ->columnSpan(1),

                    Textarea::make('address')
                        ->label('Alamat')
                        ->rows(3)
                        ->maxLength(500)
                        ->columnSpanFull(),

                    FileUpload::make('avatar_url')
                        ->label('Foto Profil')
                        ->image()
                        ->imageEditor()
                        ->imageAspectRatio([
                            '1:1',
                        ])
                        ->automaticallyCropImagesToAspectRatio()
                        ->disk('public')
                        ->directory('avatars')
                        ->visibility('public')
                        ->maxSize(2048)
                        ->helperText('Format: JPG, PNG. Maksimal 2MB. Rasio 1:1 (kotak)')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Pengaturan Akun & Role')
                ->description('Status akun dan hak akses pengguna')
                ->schema([
                    Toggle::make('is_active')
                        ->label('Status Aktif')
                        ->default(true)
                        ->helperText('Nonaktifkan untuk melarang pengguna login')
                ->columnSpan(1),

                    Select::make('role')
                        ->label('Role Utama (Sistem)')
                        ->options(fn () => \Spatie\Permission\Models\Role::whereIn('name', ['admin', 'moderator', 'user'])->pluck('name', 'name'))
                        ->required()
                        ->native(false)
                        ->searchable()
                        ->helperText('Pilih satu role untuk pengguna ini')
                        ->columnSpan(1),
                ])
                ->columns(2),
            ]);
    }
}
