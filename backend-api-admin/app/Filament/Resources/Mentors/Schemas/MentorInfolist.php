<?php

namespace App\Filament\Resources\Mentors\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;

class MentorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Mentor')
                    ->schema([
                        ImageEntry::make('photo_url')
                            ->label('Foto Profil')
                            ->defaultImageUrl(url('/images/default-avatar.png'))
                            ->circular(),

                        TextEntry::make('name')
                            ->label('Nama Lengkap')
                            ->weight(FontWeight::Bold)
                            ->size(TextSize::Large)
                            ->color('primary'),
                        
                        TextEntry::make('title')
                            ->label('Gelar/Jabatan')
                            ->badge()
                            ->color('success')
                            ->placeholder('-'),
                        
                        TextEntry::make('expertise')
                            ->label('Keahlian')
                            ->badge()
                            ->color('warning')
                            ->placeholder('-'),

                        TextEntry::make('bio')
                            ->label('Biografi')
                            ->html()
                            ->placeholder('Belum ada biografi'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Statistik')
                    ->schema([
                        TextEntry::make('total_webinars')
                            ->label('Total Webinar')
                            ->badge()
                            ->color('primary')
                            ->icon('heroicon-o-video-camera'),
                        
                        TextEntry::make('total_courses')
                            ->label('Total Kursus')
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-o-academic-cap'),
                        
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

                Section::make('Media Sosial')
                    ->schema([
                        TextEntry::make('social_media.instagram')
                            ->label('Instagram')
                            ->url(fn ($state) => $state, shouldOpenInNewTab: true)
                            ->placeholder('-')
                            ->icon('heroicon-o-camera')
                            ->color('pink'),
                        
                        TextEntry::make('social_media.linkedin')
                            ->label('LinkedIn')
                            ->url(fn ($state) => $state, shouldOpenInNewTab: true)
                            ->placeholder('-')
                            ->icon('heroicon-o-briefcase')
                            ->color('blue'),
                        
                        TextEntry::make('social_media.twitter')
                            ->label('Twitter/X')
                            ->url(fn ($state) => $state, shouldOpenInNewTab: true)
                            ->placeholder('-')
                            ->icon('heroicon-o-at-symbol')
                            ->color('sky'),
                        
                        TextEntry::make('social_media.youtube')
                            ->label('YouTube')
                            ->url(fn ($state) => $state, shouldOpenInNewTab: true)
                            ->placeholder('-')
                            ->icon('heroicon-o-play-circle')
                            ->color('red'),
                        
                        TextEntry::make('social_media.facebook')
                            ->label('Facebook')
                            ->url(fn ($state) => $state, shouldOpenInNewTab: true)
                            ->placeholder('-')
                            ->icon('heroicon-o-share')
                            ->color('indigo'),
                        
                        TextEntry::make('social_media.website')
                            ->label('Website Pribadi')
                            ->url(fn ($state) => $state, shouldOpenInNewTab: true)
                            ->placeholder('-')
                            ->icon('heroicon-o-globe-alt')
                            ->color('gray'),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(),

                Section::make('Informasi Sistem')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime('d F Y, H:i')
                            ->timezone('Asia/Jakarta')
                            ->icon('heroicon-o-clock'),
                        
                        TextEntry::make('updated_at')
                            ->label('Terakhir Diupdate')
                            ->dateTime('d F Y, H:i')
                            ->timezone('Asia/Jakarta')
                            ->icon('heroicon-o-arrow-path')
                            ->since(),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
