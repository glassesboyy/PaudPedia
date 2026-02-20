<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;


class ArticleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Artikel')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Judul')
                            ->size('large')
                            ->weight(FontWeight::Bold)
                            ->copyable()
                            ->columnSpanFull(),

                        TextEntry::make('slug')
                            ->label('Slug')
                            ->copyable()
                            ->badge()
                            ->copyable()
                            ->color('gray'),

                        TextEntry::make('category.name')
                            ->label('Kategori')
                            ->badge()
                            ->color('info')
                            ->placeholder('Tidak ada kategori'),

                        TextEntry::make('author.name')
                            ->label('Penulis')
                            ->badge()
                            ->color('success'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Konten')
                    ->schema([
                        ImageEntry::make('featured_image_url')
                            ->label('Featured Image')
                            ->disk('public')
                            ->columnSpanFull(),

                        TextEntry::make('excerpt')
                            ->label('Ringkasan (Meta Description)')
                            ->columnSpanFull()
                            ->placeholder('Tidak ada ringkasan'),

                        TextEntry::make('content')
                            ->label('Konten Artikel')
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Tags & Statistik')
                    ->schema([
                        TextEntry::make('tags')
                            ->label('Tags')
                            ->badge()
                            ->separator(',')
                            ->columnSpanFull()
                            ->placeholder('Tidak ada tags'),

                        TextEntry::make('view_count')
                            ->label('Total Views')
                            ->numeric()
                            ->color('info'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Status Publikasi')
                    ->schema([
                        IconEntry::make('is_published')
                            ->label('Dipublikasikan')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('gray'),

                        TextEntry::make('published_at')
                            ->label('Tanggal Publikasi')
                            ->dateTime('d M Y, H:i')
                            ->timezone('Asia/Jakarta')
                            ->placeholder('Belum dipublikasikan'),

                        IconEntry::make('is_featured')
                            ->label('Featured')
                            ->boolean()
                            ->trueIcon('heroicon-o-star')
                            ->falseIcon('heroicon-o-star')
                            ->trueColor('warning')
                            ->falseColor('gray'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Section::make('Informasi Sistem')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d M Y, H:i')
                            ->timezone('Asia/Jakarta'),

                        TextEntry::make('updated_at')
                            ->label('Diperbarui')
                            ->dateTime('d M Y, H:i')
                            ->timezone('Asia/Jakarta')
                            ->since(),

                        TextEntry::make('deleted_at')
                            ->label('Dihapus')
                            ->dateTime('d M Y, H:i')
                            ->timezone('Asia/Jakarta')
                            ->placeholder('Tidak dihapus')
                            ->visible(fn ($record) => $record?->trashed()),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ]);
    }
}
