<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Artikel')
                    ->description('Isi informasi dasar artikel')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Artikel')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, $set) => 
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            )
                            ->placeholder('Masukkan judul artikel')
                            ->helperText('Judul akan ditampilkan di halaman publik'),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique('articles', 'slug', ignoreRecord: true)
                            ->placeholder('contoh: tips-mengajar-paud')
                            ->helperText('URL friendly (otomatis terisi dari judul)'),

                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name', function ($query) {
                                $query->where('type', 'article');
                            })
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih kategori')
                            ->helperText('Kategori artikel untuk memudahkan pencarian'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Konten Artikel')
                    ->description('Tulis konten artikel dengan rich text editor')
                    ->schema([
                        RichEditor::make('content')
                            ->label('Konten')
                            ->required()
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike', 'link'],
                                ['h1', 'h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                ['attachFiles'],
                                ['undo', 'redo'],
                            ])
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('articles/attachments')
                            ->fileAttachmentsVisibility('public')
                            ->placeholder('Tulis konten artikel di sini...')
                            ->helperText('Gunakan toolbar untuk formatting teks dan upload gambar.')
                            ->columnSpanFull(),

                        Textarea::make('excerpt')
                            ->label('Ringkasan (Meta Description)')
                            ->rows(3)
                            ->maxLength(160)
                            ->placeholder('Ringkasan singkat untuk SEO dan preview...')
                            ->helperText('Maksimal 160 karakter untuk optimasi SEO'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Featured Image')
                    ->description('Upload gambar utama artikel')
                    ->schema([
                        FileUpload::make('featured_image_url')
                            ->label('Gambar Utama')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('articles/images')
                            ->imageEditor()
                            ->maxSize(2048) // 2MB
                            ->helperText('Gambar preview artikel. Maksimal 2MB'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Tags & Metadata')
                    ->description('Tambahkan tags untuk artikel')
                    ->schema([
                        TagsInput::make('tags')
                            ->label('Tags')
                            ->placeholder('Ketik dan tekan Enter')
                            ->helperText('Tags membantu kategorisasi dan pencarian artikel'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Publikasi')
                    ->description('Atur status publikasi artikel')
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Publikasikan')
                            ->default(false)
                            ->helperText('Artikel yang dipublikasi akan muncul di halaman publik')
                            ->inline(false)
                            ->live(),

                        DateTimePicker::make('published_at')
                            ->label('Tanggal Publikasi')
                            ->seconds(false)
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->timezone('Asia/Jakarta')
                            ->helperText('Tanggal artikel dipublikasikan')
                            ->visible(fn ($get) => $get('is_published')),

                        Toggle::make('is_featured')
                            ->label('Jadikan Featured')
                            ->default(false)
                            ->helperText('Artikel featured akan ditampilkan di homepage')
                            ->inline(false),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
