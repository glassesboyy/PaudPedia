<?php

namespace App\Filament\Resources\Webinars\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class WebinarInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Webinar')
                    ->schema([
                        ImageEntry::make('thumbnail_url')
                            ->label('Thumbnail')
                            ->defaultImageUrl(url('/images/default-webinar.png')),

                        TextEntry::make('title')
                            ->label('Judul Webinar')
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large)
                            ->color('primary'),
                        
                        TextEntry::make('slug')
                            ->label('Slug'),
                        
                        TextEntry::make('mentor.name')
                            ->label('Mentor')
                            ->color('success'),

                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->html()
                            ->placeholder('Tidak ada deskripsi'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Jadwal & Durasi')
                    ->schema([
                        TextEntry::make('scheduled_at')
                            ->label('Jadwal')
                            ->dateTime('d F Y, H:i')
                            ->timezone('Asia/Jakarta')
                            ->color('warning'),
                        
                        TextEntry::make('duration_minutes')
                            ->label('Durasi')
                            ->suffix(' menit')
                            ->placeholder('-'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Harga & Peserta')
                    ->schema([
                        TextEntry::make('price')
                            ->label('Harga Saat Ini')
                            ->money('IDR')
                            ->color('success')
                            ->weight(FontWeight::Bold),
                        
                        TextEntry::make('original_price')
                            ->label('Harga Asli')
                            ->money('IDR')
                            ->placeholder('Tidak ada diskon')
                            ->color('gray'),
                        
                        TextEntry::make('discount_percentage')
                            ->label('Diskon')
                            ->suffix('%')
                            ->badge()
                            ->color('danger')
                            ->placeholder('-'),
                        
                        TextEntry::make('max_participants')
                            ->label('Maksimal Peserta')
                            ->numeric()
                            ->placeholder('Unlimited'),
                        
                        TextEntry::make('total_purchases')
                            ->label('Jumlah Terjual')
                            ->badge()
                            ->color('primary'),
                        
                        IconEntry::make('is_active')
                            ->label('Status Aktif')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Informasi Zoom')
                    ->schema([
                        TextEntry::make('zoom_link')
                            ->label('Link Zoom Meeting')
                            ->url(fn ($state) => $state, shouldOpenInNewTab: true)
                            ->placeholder('Belum ada link Zoom')
                            ->color('primary'),
                        
                        TextEntry::make('zoom_meeting_id')
                            ->label('Meeting ID')
                            ->copyable()
                            ->copyMessage('Meeting ID disalin!')
                            ->placeholder('-'),
                        
                        TextEntry::make('zoom_passcode')
                            ->label('Passcode')
                            ->copyable()
                            ->copyMessage('Passcode disalin!')
                            ->placeholder('-'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Informasi Sistem')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime('d F Y, H:i')
                            ->timezone('Asia/Jakarta'),
                        
                        TextEntry::make('updated_at')
                            ->label('Terakhir Diupdate')
                            ->dateTime('d F Y, H:i')
                            ->timezone('Asia/Jakarta')
                            ->since(),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
