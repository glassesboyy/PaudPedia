<?php

namespace App\Filament\Resources\Webinars\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class WebinarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Webinar')
                    ->description('Isi informasi dasar webinar')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Webinar')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, $set) => 
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            )
                            ->placeholder('Masukkan judul webinar')
                            ->helperText('Judul akan ditampilkan di halaman publik'),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique('webinars', 'slug', ignoreRecord: true)
                            ->placeholder('contoh: strategi-mengajar-paud')
                            ->helperText('URL friendly (otomatis terisi dari judul)'),

                        Select::make('mentor_id')
                            ->label('Mentor')
                            ->required()
                            ->relationship('mentor', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Dr. Ahmad Sulaiman, M.Psi'),

                                TextInput::make('title')
                                    ->label('Gelar/Jabatan')
                                    ->maxLength(255)
                                    ->placeholder('M.Psi, Psikolog')
                                    ->helperText('Gelar akademik atau jabatan profesional'),

                                TextInput::make('expertise')
                                    ->label('Keahlian')
                                    ->maxLength(255)
                                    ->placeholder('Parenting Expert, Child Development')
                                    ->helperText('Bidang keahlian mentor'),

                                FileUpload::make('photo_url')
                                    ->label('Foto Profil')
                                    ->image()
                                    ->directory('mentors/photos')
                                    ->imageEditor()
                                    ->maxSize(1024)
                                    ->helperText('Ukuran maksimal 1MB'),

                                Textarea::make('bio')
                                    ->label('Biografi')
                                    ->rows(4)
                                    ->placeholder('Ceritakan tentang pengalaman dan latar belakang mentor...')
                                    ->helperText('Deskripsi singkat tentang mentor'),

                                TextInput::make('social_media.instagram')
                                    ->label('Instagram')
                                    ->url()
                                    ->placeholder('https://instagram.com/username')
                                    ->prefixIcon('heroicon-o-camera'),

                                TextInput::make('social_media.linkedin')
                                    ->label('LinkedIn')
                                    ->url()
                                    ->placeholder('https://linkedin.com/in/username')
                                    ->prefixIcon('heroicon-o-briefcase'),

                                TextInput::make('social_media.twitter')
                                    ->label('Twitter/X')
                                    ->url()
                                    ->placeholder('https://twitter.com/username')
                                    ->prefixIcon('heroicon-o-at-symbol'),

                                TextInput::make('social_media.youtube')
                                    ->label('YouTube')
                                    ->url()
                                    ->placeholder('https://youtube.com/@username')
                                    ->prefixIcon('heroicon-o-play-circle'),

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true)
                                    ->helperText('Hanya mentor aktif yang dapat dipilih')
                                    ->inline(false),
                            ])
                            ->placeholder('Pilih mentor')
                            ->helperText('Mentor yang akan mengisi webinar'),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->rows(4)
                            ->placeholder('Jelaskan tentang webinar ini...')
                            ->helperText('Deskripsi detail tentang materi webinar'),

                        FileUpload::make('thumbnail_url')
                            ->label('Thumbnail')
                            ->image()
                            ->directory('webinars/thumbnails')
                            ->imageEditor()
                            ->maxSize(2048)
                            ->helperText('Ukuran maksimal 2MB, rasio 16:9'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Harga & Diskon')
                    ->description('Atur harga webinar')
                    ->schema([
                        TextInput::make('price')
                            ->label('Harga')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->default(0)
                            ->placeholder('100000')
                            ->helperText('Harga saat ini'),

                        TextInput::make('original_price')
                            ->label('Harga Asli (Opsional)')
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->placeholder('150000')
                            ->helperText('Jika ada diskon, isi harga sebelum diskon')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $get, $set) {
                                $price = $get('price');
                                if ($state && $price && $state > $price) {
                                    $discount = round((($state - $price) / $state) * 100);
                                    Notification::make()
                                        ->title('Diskon: ' . $discount . '%')
                                        ->success()
                                        ->send();
                                }
                            }),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Jadwal & Zoom')
                    ->description('Atur jadwal dan link Zoom')
                    ->schema([
                        DateTimePicker::make('scheduled_at')
                            ->label('Jadwal Pelaksanaan')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->timezone('Asia/Jakarta')
                            ->minDate(now())
                            ->helperText('Pilih tanggal dan waktu webinar'),

                        TextInput::make('duration_minutes')
                            ->label('Durasi (Menit)')
                            ->required()
                            ->numeric()
                            ->suffix('menit')
                            ->minValue(1)
                            ->default(90)
                            ->placeholder('90')
                            ->helperText('Estimasi durasi webinar'),

                        TextInput::make('zoom_link')
                            ->label('Link Zoom')
                            ->url()
                            ->placeholder('https://zoom.us/j/1234567890')
                            ->helperText('Link meeting Zoom (buat dulu di zoom.us)'),

                        TextInput::make('zoom_meeting_id')
                            ->label('Meeting ID')
                            ->placeholder('123 4567 8901')
                            ->helperText('ID meeting Zoom'),

                        TextInput::make('zoom_passcode')
                            ->label('Passcode')
                            ->placeholder('******')
                            ->password()
                            ->revealable()
                            ->helperText('Password meeting Zoom (jika ada)'),

                        TextInput::make('max_participants')
                            ->label('Maksimal Peserta')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('100')
                            ->helperText('Batasan jumlah peserta (kosongkan untuk unlimited)'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Status')
                    ->description('Atur status publikasi')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Hanya webinar aktif yang ditampilkan di halaman publik')
                            ->inline(false),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
