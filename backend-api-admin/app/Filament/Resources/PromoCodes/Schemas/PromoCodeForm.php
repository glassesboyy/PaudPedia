<?php

namespace App\Filament\Resources\PromoCodes\Schemas;

use App\Enums\DiscountType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PromoCodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Kode Promo')
                    ->description('Data dasar kode promo')
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Promo')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Contoh: DISKON50, PROMO2024')
                            ->helperText('Kode harus unik dan mudah diingat')
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('code', strtoupper($state)))
                            ->columnSpan(1),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Deskripsi singkat tentang promo ini...')
                            ->helperText('Jelaskan tujuan atau ketentuan promo (maksimal 500 karakter)')
                            ->columnSpan(1),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Pengaturan Diskon')
                    ->description('Jenis dan nilai diskon')
                    ->schema([
                        Select::make('discount_type')
                            ->label('Tipe Diskon')
                            ->options(DiscountType::class)
                            ->required()
                            ->native(false)
                            ->live()
                            ->helperText('Pilih antara diskon persentase atau nominal tetap')
                            ->columnSpan(1),

                        TextInput::make('discount_value')
                            ->label(fn ($get) => $get('discount_type') == DiscountType::PERCENTAGE 
                                ? 'Nilai Diskon (%)' 
                                : 'Nilai Diskon (Rp)')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(fn ($get) => $get('discount_type') == DiscountType::PERCENTAGE ? 100 : 999999999)
                            ->prefix(fn ($get) => $get('discount_type') == DiscountType::FIXED ? 'Rp' : '')
                            ->suffix(fn ($get) => $get('discount_type') == DiscountType::PERCENTAGE ? '%' : '')
                            ->placeholder(fn ($get) => $get('discount_type') == DiscountType::PERCENTAGE 
                                ? 'Contoh: 50 (untuk 50%)' 
                                : 'Contoh: 100000 (untuk Rp 100.000)')
                            ->helperText(fn ($get) => $get('discount_type') == DiscountType::PERCENTAGE 
                                ? 'Masukkan nilai 0-100 untuk persentase diskon' 
                                : 'Masukkan nominal diskon dalam Rupiah')
                            ->columnSpan(1),

                        TextInput::make('max_discount_amount')
                            ->label('Maksimal Potongan (Rp)')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->placeholder('Contoh: 500000 (untuk Rp 500.000)')
                            ->helperText('Opsional - Batas maksimal potongan untuk diskon persentase')
                            ->visible(fn ($get) => $get('discount_type') == DiscountType::PERCENTAGE)
                            ->columnSpan(1),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Ketentuan Penggunaan')
                    ->description('Batasan dan aturan penggunaan promo')
                    ->schema([
                        TextInput::make('min_purchase_amount')
                            ->label('Minimal Pembelian (Rp)')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->placeholder('Contoh: 100000 (untuk Rp 100.000)')
                            ->helperText('Opsional - Minimal total belanja untuk menggunakan promo')
                            ->columnSpan(1),

                        TextInput::make('usage_limit')
                            ->label('Batas Penggunaan')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('Contoh: 100 (hanya bisa dipakai 100 kali)')
                            ->helperText('Opsional - Jumlah maksimal kode bisa digunakan. Kosongkan untuk unlimited')
                            ->columnSpan(1),

                        TextInput::make('usage_count')
                            ->label('Sudah Digunakan')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Otomatis bertambah setiap kali promo digunakan')
                            ->visible(fn ($record) => $record !== null)
                            ->columnSpan(1),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Periode Aktif')
                    ->description('Waktu mulai dan berakhir promo')
                    ->schema([
                        DateTimePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->default(now())
                            ->helperText('Promo mulai berlaku dari tanggal ini')
                            ->columnSpan(1),

                        DateTimePicker::make('end_date')
                            ->label('Tanggal Berakhir')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false)
                            ->after('start_date')
                            ->placeholder('Kosongkan jika tidak ada batas waktu')
                            ->helperText('Opsional - Promo berakhir pada tanggal ini. Kosongkan untuk unlimited')
                            ->columnSpan(1),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Status')
                    ->description('Aktifkan atau nonaktifkan kode promo')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Nonaktifkan untuk menangguhkan promo tanpa menghapus')
                            ->columnSpan(1),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
