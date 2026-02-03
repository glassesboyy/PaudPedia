<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Pemberi Testimoni')
                    ->description('Data identitas pemberi testimoni')
                    ->schema([
                        Select::make('user_id')
                            ->label('Pengguna Terdaftar')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih pengguna (opsional)')
                            ->helperText('Pilih pengguna terdaftar atau kosongkan untuk mengisi nama manual'),

                        TextInput::make('name')
                            ->label('Nama Pemberi Testimoni')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Budi Santoso')
                            ->helperText('Nama akan otomatis diambil dari pengguna jika dipilih'),

                        TextInput::make('title')
                            ->label('Jabatan/Posisi')
                            ->maxLength(255)
                            ->placeholder('Contoh: Kepala Sekolah TK Melati')
                            ->helperText('Jabatan atau posisi pemberi testimoni (opsional)'),

                        FileUpload::make('photo_url')
                            ->label('Foto Profil')
                            ->image()
                            ->disk('public')
                            ->directory('testimonials')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->imageAspectRatio([
                                '1:1'
                            ])
                            ->automaticallyCropImagesToAspectRatio()
                            ->helperText('Upload foto profil (maksimal 2MB, rasio 1:1)'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Konten Testimoni')   
                    ->description('Isi dan rating testimoni')
                    ->schema([
                        Textarea::make('content')
                            ->label('Isi Testimoni')
                            ->required()
                            ->rows(5)
                            ->maxLength(1000)
                            ->placeholder('Tuliskan testimoni di sini...')
                            ->helperText('Maksimal 1000 karakter'),

                        Select::make('rating')
                            ->label('Rating Bintang')
                            ->required()
                            ->options([
                                1 => '⭐ 1 Bintang',
                                2 => '⭐⭐ 2 Bintang',
                                3 => '⭐⭐⭐ 3 Bintang',
                                4 => '⭐⭐⭐⭐ 4 Bintang',
                                5 => '⭐⭐⭐⭐⭐ 5 Bintang',
                            ])
                            ->default(5)
                            ->helperText('Pilih rating untuk testimoni'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Pengaturan Tampilan')
                    ->description('Atur status persetujuan dan featured')
                    ->schema([
                        Toggle::make('is_approved')
                            ->label('Disetujui')
                            ->default(false)
                            ->helperText('Testimoni yang disetujui akan tampil di website')
                            ->reactive()
                            ->afterStateUpdated(fn ($set, $state) => !$state ? $set('is_featured', false) : null)
                            ->inline(false),

                        Toggle::make('is_featured')
                            ->label('Ditampilkan')
                            ->default(false)
                            ->helperText('Testimoni unggulan akan ditampilkan di halaman utama (harus disetujui terlebih dahulu)')
                            ->disabled(fn ($get) => !$get('is_approved'))
                            ->dehydrated()
                            ->reactive()
                            ->afterStateUpdated(fn ($set, $state, $get) => 
                                !$get('is_approved') && $state ? $set('is_featured', false) : null
                            )
                            ->inline(false),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
