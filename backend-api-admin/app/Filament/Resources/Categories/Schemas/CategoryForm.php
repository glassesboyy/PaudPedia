<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Enums\CategoryType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kategori')
                    ->description('Isi informasi dasar kategori')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, $set) => 
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            )
                            ->placeholder('Masukkan nama kategori')
                            ->helperText('Nama kategori akan ditampilkan di seluruh sistem'),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique('categories', 'slug', ignoreRecord: true)
                            ->placeholder('contoh: kategori-paud')
                            ->helperText('URL friendly (otomatis terisi dari nama kategori)'),

                        Select::make('type')
                            ->label('Tipe Kategori')
                            ->options([
                                CategoryType::COURSE->value => CategoryType::COURSE->label(),
                                CategoryType::PRODUCT->value => CategoryType::PRODUCT->label(),
                                CategoryType::ARTICLE->value => CategoryType::ARTICLE->label(),
                            ])
                            ->required()
                            ->native(false)
                            ->placeholder('Pilih tipe kategori')
                            ->helperText('Pilih tipe kategori: Kursus, Produk, atau Artikel'),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(5)
                            ->maxLength(500)
                            ->placeholder('Masukkan deskripsi kategori (opsional)')
                            ->helperText('Deskripsi singkat tentang kategori ini'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
