<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
            Section::make('Informasi Pengguna')
                ->schema([
                    ImageEntry::make('avatar_url')
                        ->label('Foto Profil')
                        ->disk('public')
                        ->imageHeight(150)
                        ->defaultImageUrl(url('/images/default-avatar.png'))
                        ->columnSpanFull(),

                    TextEntry::make('name')
                        ->label('Nama Lengkap')
                        ->weight(FontWeight::Bold)
                        ->columnSpan(1),

                    TextEntry::make('email')
                        ->label('Email')
                        ->icon('heroicon-m-envelope')
                        ->copyable()
                        ->copyMessage('Email disalin!')
                        ->columnSpan(1),

                    TextEntry::make('phone')
                        ->label('Nomor Telepon')
                        ->icon('heroicon-m-phone')
                        ->placeholder('Tidak ada')
                        ->columnSpan(1),

                    TextEntry::make('is_active')
                        ->label('Status Akun')
                        ->badge()
                        ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                        ->formatStateUsing(fn (bool $state): string => $state ? 'Aktif' : 'Nonaktif')
                        ->columnSpan(1),
                ])
                ->columns(2),

            Section::make('Informasi Pribadi')
                ->schema([
                    TextEntry::make('gender')
                        ->label('Jenis Kelamin')
                        ->badge()
                        ->placeholder('Tidak diketahui')
                        ->columnSpan(1),

                    TextEntry::make('date_of_birth')
                        ->label('Tanggal Lahir')
                        ->date('d F Y')
                        ->placeholder('Tidak diketahui')
                        ->columnSpan(1),

                    TextEntry::make('age')
                        ->label('Usia')
                        ->suffix(' tahun')
                        ->placeholder('-')
                        ->columnSpan(1),

                    TextEntry::make('address')
                        ->label('Alamat')
                        ->placeholder('Tidak ada')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Role & Hak Akses')
                ->schema([
                    TextEntry::make('roles.name')
                        ->label('Role')
                        ->badge()
                        ->color('primary')
                        ->separator(', ')
                        ->placeholder('Tidak ada role')
                        ->columnSpanFull(),
                ]),

            Section::make('Informasi Akun')
                ->schema([
                    TextEntry::make('email_verified_at')
                        ->label('Email Terverifikasi')
                        ->dateTime('d F Y, H:i')
                        ->placeholder('Belum diverifikasi')
                        ->icon('heroicon-m-check-badge')
                        ->columnSpan(1),

                    TextEntry::make('created_at')
                        ->label('Terdaftar Sejak')
                        ->dateTime('d F Y, H:i')
                        ->columnSpan(1),

                    TextEntry::make('updated_at')
                        ->label('Terakhir Diperbarui')
                        ->dateTime('d F Y, H:i')
                        ->since()
                        ->columnSpan(1),
                ])
                ->columns(2),
            ]);
    }
}
