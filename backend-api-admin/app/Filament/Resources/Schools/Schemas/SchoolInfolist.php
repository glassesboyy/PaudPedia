<?php

namespace App\Filament\Resources\Schools\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class SchoolInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
            Section::make('Informasi Sekolah')
                ->schema([
                    ImageEntry::make('logo_url')
                        ->label('Logo Sekolah')
                        ->disk('public')
                        ->defaultImageUrl(url('/images/default-school-logo.png'))
                        ->columnSpanFull(),

                    TextEntry::make('name')
                        ->label('Nama Sekolah')
                        ->weight(FontWeight::Bold)
                        ->columnSpan(1),

                    TextEntry::make('npsn')
                        ->label('NPSN')
                        ->copyable()
                        ->copyMessage('NPSN disalin!')
                        ->placeholder('Tidak ada')
                        ->columnSpan(1),

                    TextEntry::make('phone')
                        ->label('Nomor Telepon')
                        ->icon('heroicon-m-phone')
                        ->placeholder('Tidak ada')
                        ->columnSpan(1),

                    TextEntry::make('email')
                        ->label('Email')
                        ->icon('heroicon-m-envelope')
                        ->copyable()
                        ->copyMessage('Email disalin!')
                        ->placeholder('Tidak ada')
                        ->columnSpan(1),

                    TextEntry::make('address')
                        ->label('Alamat')
                        ->placeholder('Tidak ada')
                        ->columnSpanFull(),

                    TextEntry::make('is_active')
                        ->label('Status')
                        ->badge()
                        ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                        ->formatStateUsing(fn (bool $state): string => $state ? 'Aktif' : 'Nonaktif')
                        ->columnSpan(1),
                ])
                ->columns(2),

            Section::make('Subscription & Batasan')
                ->schema([
                    TextEntry::make('subscription_plan')
                        ->label('Paket Langganan')
                        ->badge()
                        ->color(fn (string $state): string => match($state) {
                            'pro' => 'success',
                            'free' => 'warning',
                            default => 'gray',
                        })
                        ->columnSpan(1),

                    TextEntry::make('subscription_expires_at')
                        ->label('Kadaluarsa')
                        ->date('d F Y')
                        ->placeholder('Tidak ada batas')
                        ->color(fn ($state) => $state && now()->greaterThan($state) ? 'danger' : 'success')
                        ->icon(fn ($state) => $state && now()->greaterThan($state) ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-circle')
                        ->columnSpan(1),

                    TextEntry::make('student_limit')
                        ->label('Batas Siswa')
                        ->formatStateUsing(fn ($state) => $state >= 9999 ? 'Unlimited' : number_format($state))
                        ->columnSpan(1),

                    TextEntry::make('teacher_limit')
                        ->label('Batas Guru')
                        ->formatStateUsing(fn ($state) => $state >= 9999 ? 'Unlimited' : number_format($state))
                        ->columnSpan(1),
                ])
                ->columns(2),

            Section::make('Statistik')
                ->schema([
                    TextEntry::make('students_count')
                        ->label('Total Siswa')
                        ->numeric()
                        ->icon('heroicon-m-academic-cap')
                        ->columnSpan(1),

                    TextEntry::make('teachers_count')
                        ->label('Total Guru')
                        ->numeric()
                        ->icon('heroicon-m-users')
                        ->columnSpan(1),

                    TextEntry::make('classes_count')
                        ->label('Total Kelas')
                        ->numeric()
                        ->icon('heroicon-m-building-library')
                        ->columnSpan(1),
                ])
                ->columns(3),

            Section::make('Informasi Sistem')
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Dibuat Pada')
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
