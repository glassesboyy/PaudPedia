<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Produk')
                    ->description('Isi informasi dasar produk digital')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Produk')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, $set) => 
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            )
                            ->placeholder('Masukkan judul produk')
                            ->helperText('Judul akan ditampilkan di halaman publik'),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique('products', 'slug', ignoreRecord: true)
                            ->placeholder('contoh: template-rpph-paud')
                            ->helperText('URL friendly (otomatis terisi dari judul)'),

                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name', function ($query) {
                                $query->where('type', 'product');
                            })
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih kategori')
                            ->helperText('Kategori produk untuk memudahkan pencarian'),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->rows(5)
                            ->placeholder('Jelaskan tentang produk ini...')
                            ->helperText('Deskripsi detail tentang isi dan manfaat produk'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('File Produk')
                    ->description('Upload file produk digital')
                    ->schema([
                        FileUpload::make('file_url')
                            ->label('File Produk')
                            ->required()
                            ->directory('products/files')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/zip',
                                'application/x-zip-compressed',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-powerpoint',
                                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            ])
                            ->maxSize(51200) // 50MB
                            ->downloadable()
                            ->openable()
                            ->helperText('Format: PDF, ZIP, DOC, DOCX, PPT, PPTX. Maksimal 50MB'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Thumbnail')
                    ->description('Upload gambar thumbnail produk')
                    ->schema([
                        FileUpload::make('thumbnail_url')
                            ->label('Thumbnail Produk')
                            ->image()
                            ->directory('products/thumbnails')
                            ->imageEditor()
                            ->maxSize(2048) // 2MB
                            ->helperText('Gambar preview produk. Maksimal 2MB'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Harga')
                    ->description('Atur harga produk')
                    ->schema([
                        TextInput::make('price')
                            ->label('Harga Saat Ini')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->default(0)
                            ->placeholder('0')
                            ->helperText('Harga jual produk saat ini'),

                        TextInput::make('original_price')
                            ->label('Harga Asli (Opsional)')
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->placeholder('0')
                            ->helperText('Harga coret untuk menampilkan diskon. Kosongkan jika tidak ada diskon'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Status')
                    ->description('Atur status publikasi')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Hanya produk aktif yang ditampilkan di halaman publik')
                            ->inline(false),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
