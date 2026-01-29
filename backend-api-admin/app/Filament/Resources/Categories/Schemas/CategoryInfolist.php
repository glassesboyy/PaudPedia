<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Kategori')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Kategori')
                            ->size('large')
                            ->weight(FontWeight::Bold)
                            ->copyable()
                            ->columnSpanFull(),

                        TextEntry::make('slug')
                            ->label('Slug')
                            ->badge()
                            ->copyable()
                            ->color('gray'),

                        TextEntry::make('type')
                            ->label('Tipe')
                            ->badge()
                            ->color(fn($state) => match($state instanceof \App\Enums\CategoryType ? $state->value : $state) {
                                'course' => 'success',
                                'product' => 'warning',
                                'article' => 'info',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn($state) => $state instanceof \App\Enums\CategoryType ? $state->label() : $state),

                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull()
                            ->placeholder('Tidak ada deskripsi'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Statistik Penggunaan')
                    ->schema([
                        TextEntry::make('courses_count')
                            ->label('Total Kursus')
                            ->badge()
                            ->color('success')
                            ->getStateUsing(fn ($record) => $record->courses()->count()),

                        TextEntry::make('products_count')
                            ->label('Total Produk')
                            ->badge()
                            ->color('warning')
                            ->getStateUsing(fn ($record) => $record->products()->count()),

                        TextEntry::make('articles_count')
                            ->label('Total Artikel')
                            ->badge()
                            ->color('info')
                            ->getStateUsing(fn ($record) => $record->articles()->count()),
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
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
