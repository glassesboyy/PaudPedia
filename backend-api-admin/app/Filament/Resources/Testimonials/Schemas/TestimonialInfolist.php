<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class TestimonialInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Pemberi Testimoni')
                    ->schema([
                        ImageEntry::make('photo_url')
                            ->label('Foto Profil')
                            ->disk('public')
                            ->circular()
                            ->defaultImageUrl(url('/images/default-avatar.png')),

                        TextEntry::make('display_name')
                            ->label('Nama')
                            ->size('large')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('user.email')
                            ->label('Email Pengguna')
                            ->placeholder('Tidak terdaftar sebagai pengguna')
                            ->icon('heroicon-o-envelope'),

                        TextEntry::make('title')
                            ->label('Jabatan/Posisi')
                            ->placeholder('-')
                            ->icon('heroicon-o-briefcase'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Konten Testimoni')
                    ->schema([
                        TextEntry::make('content')
                            ->label('Isi Testimoni')
                            ->columnSpanFull()
                            ->prose(),

                        TextEntry::make('rating')
                            ->label('Rating')
                            ->badge()
                            ->color(fn ($state) => match (true) {
                                $state >= 4 => 'success',
                                $state >= 3 => 'warning',
                                default => 'danger',
                            })
                            ->formatStateUsing(fn ($state) => str_repeat('⭐', $state) . " ($state/5)"),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Status & Pengaturan')
                    ->schema([
                        IconEntry::make('is_approved')
                            ->label('Status Persetujuan')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),

                        IconEntry::make('is_featured')
                            ->label('Testimoni Unggulan')
                            ->boolean()
                            ->trueIcon('heroicon-o-star')
                            ->falseIcon('heroicon-o-star')
                            ->trueColor('warning')
                            ->falseColor('gray'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Informasi Sistem')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime('d M Y, H:i')
                            ->icon('heroicon-o-clock'),

                        TextEntry::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->dateTime('d M Y, H:i')
                            ->since()
                            ->icon('heroicon-o-arrow-path'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
