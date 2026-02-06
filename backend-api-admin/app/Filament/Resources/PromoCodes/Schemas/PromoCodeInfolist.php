<?php

namespace App\Filament\Resources\PromoCodes\Schemas;

use App\Enums\DiscountType;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class PromoCodeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Kode Promo')
                    ->schema([
                        TextEntry::make('code')
                            ->label('Kode Promo')
                            ->weight(FontWeight::Bold)
                            ->size('lg')
                            ->copyable()
                            ->copyMessage('Kode promo disalin!')
                            ->badge()
                            ->color('success')
                            ->columnSpan(1),

                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->placeholder('Tidak ada deskripsi')
                            ->columnSpan(1),

                        TextEntry::make('is_active')
                            ->label('Status')
                            ->badge()
                            ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Aktif' : 'Nonaktif')
                            ->columnSpan(1),
                    ])
                    ->columns(1),

                Section::make('Detail Diskon')
                    ->schema([
                        TextEntry::make('discount_type')
                            ->label('Tipe Diskon')
                            ->badge()
                            ->color(fn ($state) => match($state) {
                                DiscountType::PERCENTAGE => 'info',
                                DiscountType::FIXED => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => $state instanceof DiscountType ? $state->label() : $state)
                            ->columnSpan(1),

                        TextEntry::make('discount_value')
                            ->label('Nilai Diskon')
                            ->weight(FontWeight::Bold)
                            ->size('lg')
                            ->formatStateUsing(fn ($state, $record) => 
                                $record->discount_type === DiscountType::PERCENTAGE 
                                    ? $state . '%' 
                                    : 'Rp ' . number_format($state, 0, ',', '.')
                            )
                            ->color('success')
                            ->columnSpan(1),

                        TextEntry::make('max_discount_amount')
                            ->label('Maksimal Potongan')
                            ->money('IDR')
                            ->placeholder('Tidak ada batas')
                            ->visible(fn ($record) => $record->discount_type === DiscountType::PERCENTAGE)
                            ->columnSpan(1),
                    ])
                    ->columns(1),

                Section::make('Ketentuan Penggunaan')
                    ->schema([
                        TextEntry::make('min_purchase_amount')
                            ->label('Minimal Pembelian')
                            ->money('IDR')
                            ->placeholder('Tidak ada minimal')
                            ->columnSpan(1),

                        TextEntry::make('usage_limit')
                            ->label('Batas Penggunaan')
                            ->formatStateUsing(fn ($state) => $state ? number_format($state) . ' kali' : 'Unlimited')
                            ->columnSpan(1),

                        TextEntry::make('usage_count')
                            ->label('Sudah Digunakan')
                            ->badge()
                            ->color(fn ($state, $record) => 
                                $record->usage_limit && $state >= $record->usage_limit ? 'danger' : 'primary'
                            )
                            ->formatStateUsing(fn ($state) => number_format($state) . ' kali')
                            ->columnSpan(1),
                    ])
                    ->columns(1),

                Section::make('Periode Aktif')
                    ->schema([
                        TextEntry::make('start_date')
                            ->label('Tanggal Mulai')
                            ->dateTime('d F Y, H:i')
                            ->placeholder('Tidak diatur')
                            ->icon('heroicon-m-calendar')
                            ->columnSpan(1),

                        TextEntry::make('end_date')
                            ->label('Tanggal Berakhir')
                            ->dateTime('d F Y, H:i')
                            ->placeholder('Tidak ada batas waktu')
                            ->icon('heroicon-m-calendar')
                            ->color(fn ($state) => $state && now()->greaterThan($state) ? 'danger' : 'success')
                            ->badge(fn ($state) => $state && now()->greaterThan($state))
                            ->formatStateUsing(fn ($state) => 
                                $state && now()->greaterThan($state) 
                                    ? $state->format('d F Y, H:i') . ' (Kadaluarsa)' 
                                    : $state?->format('d F Y, H:i') ?? 'Unlimited'
                            )
                            ->columnSpan(1),
                    ])
                    ->columns(1),

                Section::make('Statistik Penggunaan')
                    ->schema([
                        TextEntry::make('orders_count')
                            ->label('Total Transaksi')
                            ->counts('orders')
                            ->badge()
                            ->color('primary')
                            ->icon('heroicon-m-shopping-cart')
                            ->formatStateUsing(fn ($state) => number_format($state) . ' transaksi')
                            ->columnSpan(1),
                    ])
                    ->columns(1),

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
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
