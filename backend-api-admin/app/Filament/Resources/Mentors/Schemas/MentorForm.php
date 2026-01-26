<?php

namespace App\Filament\Resources\Mentors\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MentorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->description('Informasi utama mentor')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Dr. Ahmad Sulaiman')
                            ->helperText('Nama lengkap mentor'),

                        TextInput::make('title')
                            ->label('Gelar/Jabatan')
                            ->maxLength(255)
                            ->placeholder('M.Psi, Psikolog')
                            ->helperText('Gelar akademik atau jabatan profesional'),

                        TextInput::make('expertise')
                            ->label('Keahlian')
                            ->maxLength(255)
                            ->placeholder('Parenting Expert, Child Development')
                            ->helperText('Bidang keahlian dan spesialisasi'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Foto & Biografi')
                    ->description('Foto profil dan deskripsi mentor')
                    ->schema([
                        FileUpload::make('photo_url')
                            ->label('Foto Profil')
                            ->image()
                            ->directory('mentors/photos')
                            ->imageEditor()
                            ->maxSize(1024)
                            ->helperText('Ukuran maksimal 1MB, format: JPG, PNG'),

                        Textarea::make('bio')
                            ->label('Biografi')
                            ->rows(5)
                            ->placeholder('Ceritakan tentang pengalaman, latar belakang pendidikan, dan pencapaian mentor...')
                            ->helperText('Deskripsi singkat tentang pengalaman dan keahlian mentor'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Media Sosial')
                    ->description('Link akun media sosial mentor (opsional)')
                    ->schema([
                        TextInput::make('social_media.instagram')
                            ->label('Instagram')
                            ->url()
                            ->placeholder('https://instagram.com/username')
                            ->prefixIcon('heroicon-o-camera')
                            ->helperText('URL lengkap profil Instagram'),

                        TextInput::make('social_media.linkedin')
                            ->label('LinkedIn')
                            ->url()
                            ->placeholder('https://linkedin.com/in/username')
                            ->prefixIcon('heroicon-o-briefcase')
                            ->helperText('URL lengkap profil LinkedIn'),

                        TextInput::make('social_media.twitter')
                            ->label('Twitter/X')
                            ->url()
                            ->placeholder('https://twitter.com/username')
                            ->prefixIcon('heroicon-o-at-symbol')
                            ->helperText('URL lengkap profil Twitter'),

                        TextInput::make('social_media.youtube')
                            ->label('YouTube')
                            ->url()
                            ->placeholder('https://youtube.com/@username')
                            ->prefixIcon('heroicon-o-play-circle')
                            ->helperText('URL lengkap channel YouTube'),

                        TextInput::make('social_media.facebook')
                            ->label('Facebook')
                            ->url()
                            ->placeholder('https://facebook.com/username')
                            ->prefixIcon('heroicon-o-share')
                            ->helperText('URL lengkap profil Facebook'),

                        TextInput::make('social_media.website')
                            ->label('Website Pribadi')
                            ->url()
                            ->placeholder('https://www.website-mentor.com')
                            ->prefixIcon('heroicon-o-globe-alt')
                            ->helperText('URL website atau blog pribadi'),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->collapsed(),

                Section::make('Status')
                    ->description('Atur status mentor')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Hanya mentor aktif yang dapat dipilih untuk webinar dan kursus')
                            ->inline(false),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
