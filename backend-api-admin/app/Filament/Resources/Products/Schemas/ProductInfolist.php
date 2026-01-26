<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Produk')
                    ->schema([
                        ImageEntry::make('thumbnail_url')
                            ->label('Thumbnail')
                            ->defaultImageUrl(url('/images/default-product.png')),

                        TextEntry::make('title')
                            ->label('Judul')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('slug')
                            ->label('Slug')
                            ->copyable()
                            ->color('gray'),

                        TextEntry::make('category.name')
                            ->label('Kategori')
                            ->badge()
                            ->color('info')
                            ->placeholder('Tidak ada kategori'),

                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->html()
                            ->placeholder('Tidak ada deskripsi'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('File Produk')
                    ->schema([
                        TextEntry::make('file_url')
                            ->label('File')
                            ->url(fn (Product $record): ?string => $record->file_url ? asset('storage/' . $record->file_url) : null)
                            ->openUrlInNewTab()
                            ->color('primary')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->placeholder('Tidak ada file'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Harga & Statistik')
                    ->schema([
                        TextEntry::make('price')
                            ->label('Harga Saat Ini')
                            ->money('IDR')
                            ->color('success')
                            ->weight(FontWeight::Bold),

                        TextEntry::make('original_price')
                            ->label('Harga Asli')
                            ->money('IDR')
                            ->color('gray')
                            ->placeholder('Tidak ada diskon'),

                        TextEntry::make('discount_percentage')
                            ->label('Diskon')
                            ->getStateUsing(fn (Product $record): ?string => 
                                $record->discount_percentage ? $record->discount_percentage . '%' : null
                            )
                            ->badge()
                            ->color('danger')
                            ->placeholder('-'),

                        TextEntry::make('total_purchases')
                            ->label('Total Terjual')
                            ->badge()
                            ->color('primary')
                            ->icon('heroicon-o-shopping-cart'),

                        IconEntry::make('is_active')
                            ->label('Status')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
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

                        TextEntry::make('deleted_at')
                            ->label('Dihapus Pada')
                            ->dateTime('d F Y, H:i')
                            ->timezone('Asia/Jakarta')
                            ->color('danger')
                            ->placeholder('Tidak dihapus')
                            ->visible(fn (Product $record): bool => $record->trashed()),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
