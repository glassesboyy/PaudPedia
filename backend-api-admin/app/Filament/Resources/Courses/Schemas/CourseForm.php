<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Enums\ContentType;
use App\Enums\CourseLevel;
use App\Models\Mentor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kursus')
                    ->description('Isi informasi dasar kursus')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Kursus')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, $set) =>
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            )
                            ->placeholder('Masukkan judul kursus')
                            ->helperText('Judul akan ditampilkan di halaman publik'),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique('courses', 'slug', ignoreRecord: true)
                            ->placeholder('contoh: dasar-pendidikan-anak-usia-dini')
                            ->helperText('URL friendly (otomatis terisi dari judul)'),

                        Select::make('mentor_id')
                            ->label('Mentor')
                            ->required()
                            ->relationship('mentor', 'name', function ($query) {
                                $query->where('is_active', true);
                            })
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
                            ])
                            ->createOptionUsing(function (array $data) {
                                $data['is_active'] = true;
                                return Mentor::create($data)->getKey();
                            })
                            ->placeholder('Pilih mentor')
                            ->helperText('Mentor yang akan mengajar kursus ini'),

                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name', function ($query) {
                                $query->where('type', 'course');
                            })
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih kategori kursus')
                            ->helperText('Kategori untuk pengelompokan kursus'),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->rows(4)
                            ->placeholder('Jelaskan tentang kursus ini, apa yang akan dipelajari...')
                            ->helperText('Deskripsi detail tentang materi kursus'),

                        FileUpload::make('thumbnail_url')
                            ->label('Thumbnail Kursus')
                            ->image()
                            ->directory('courses/thumbnails')
                            ->imageEditor()
                            ->maxSize(2048)
                            ->helperText('Ukuran maksimal 2MB. Rekomendasi: 1280x720px'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Detail Kursus')
                    ->description('Atur tingkat kesulitan dan durasi')
                    ->schema([
                        Select::make('level')
                            ->label('Tingkat Kesulitan')
                            ->required()
                            ->options(
                                collect(CourseLevel::cases())
                                    ->mapWithKeys(fn (CourseLevel $level) => [$level->value => $level->label()])
                                    ->toArray()
                            )
                            ->default(CourseLevel::BEGINNER->value)
                            ->placeholder('Pilih tingkat kesulitan')
                            ->helperText('Tentukan level kesulitan kursus'),

                        TextInput::make('duration_hours')
                            ->label('Estimasi Durasi (Jam)')
                            ->numeric()
                            ->suffix('jam')
                            ->minValue(1)
                            ->placeholder('10')
                            ->helperText('Estimasi total durasi kursus dalam jam'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Harga & Diskon')
                    ->description('Atur harga kursus')
                    ->schema([
                        TextInput::make('price')
                            ->label('Harga')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->default(0)
                            ->placeholder('150000')
                            ->helperText('Harga saat ini (isi 0 untuk gratis)'),

                        TextInput::make('original_price')
                            ->label('Harga Asli (Opsional)')
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->placeholder('250000')
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

                Section::make('Modul & Konten Kursus')
                    ->description('Kelola modul, lesson, dan kuis kursus. Atur urutan dengan menggeser atau mengubah nomor urut.')
                    ->schema([
                        Repeater::make('modules')
                            ->label('Modul')
                            ->relationship('modules')
                            ->orderColumn('order')
                            ->reorderable()
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string =>
                                $state['title'] ?? 'Modul Baru'
                            )
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul Modul')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Masukkan judul modul')
                                    ->helperText('Judul modul dalam kursus'),

                                Textarea::make('description')
                                    ->label('Deskripsi Modul')
                                    ->rows(2)
                                    ->placeholder('Deskripsi singkat tentang modul ini...')
                                    ->helperText('Opsional: deskripsi isi modul'),

                                // LESSONS REPEATER
                                Repeater::make('lessons')
                                    ->label('Lesson')
                                    ->relationship('lessons')
                                    ->orderColumn('order')
                                    ->reorderable()
                                    ->collapsible()
                                    ->cloneable()
                                    ->itemLabel(fn (array $state): ?string =>
                                        ($state['title'] ?? 'Lesson Baru') . ' (' . (ContentType::tryFrom($state['content_type'] ?? '')?->label() ?? 'Tipe belum dipilih') . ')'
                                    )
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Judul Lesson')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Masukkan judul lesson')
                                            ->helperText('Judul lesson atau materi'),

                                        Select::make('content_type')
                                            ->label('Tipe Konten')
                                            ->required()
                                            ->options(
                                                collect(ContentType::cases())
                                                    ->mapWithKeys(fn (ContentType $type) => [$type->value => $type->label()])
                                                    ->toArray()
                                            )
                                            ->default(ContentType::VIDEO->value)
                                            ->live()
                                            ->placeholder('Pilih tipe konten')
                                            ->helperText('Jenis konten lesson'),

                                        // VIDEO: URL Input
                                        TextInput::make('video_url')
                                            ->label('URL Video')
                                            ->url()
                                            ->placeholder('https://youtube.com/embed/xxxxx atau https://youtu.be/xxxxx')
                                            ->helperText('Masukkan URL YouTube embed atau URL video lainnya')
                                            ->visible(fn ($get) => $get('content_type') === 'video')
                                            ->required(fn ($get) => $get('content_type') === 'video'),

                                        // PDF: File Upload
                                        FileUpload::make('pdf_file')
                                            ->label('File PDF')
                                            ->acceptedFileTypes(['application/pdf'])
                                            ->directory('courses/lessons/pdf')
                                            ->maxSize(10240) // 10MB
                                            ->downloadable()
                                            ->previewable()
                                            ->helperText('Upload file PDF (maksimal 10MB)')
                                            ->visible(fn ($get) => $get('content_type') === 'pdf')
                                            ->required(fn ($get) => $get('content_type') === 'pdf'),

                                        // TEXT: Rich Text Editor
                                        RichEditor::make('text_content')
                                            ->label('Konten Teks')
                                            ->toolbarButtons([
                                                'bold', 'italic', 'underline', 'strike',
                                                'h1', 'h2', 'h3', 'bulletList', 'orderedList'
                                            ])
                                            ->fileAttachmentsDisk('public')
                                            ->fileAttachmentsDirectory('courses/lessons/attachments')
                                            ->fileAttachmentsVisibility('public')
                                            ->placeholder('Tulis konten lesson di sini...')
                                            ->helperText('Gunakan toolbar untuk formatting teks dan upload gambar')
                                            ->visible(fn ($get) => $get('content_type') === 'text')
                                            ->required(fn ($get) => $get('content_type') === 'text'),

                                        TextInput::make('duration_minutes')
                                            ->label('Durasi (Menit)')
                                            ->numeric()
                                            ->suffix('menit')
                                            ->minValue(1)
                                            ->placeholder('15')
                                            ->helperText('Estimasi durasi lesson'),
                                    ])
                                    ->columns(1)
                                    ->defaultItems(0)
                                    ->addActionLabel('Tambah Lesson')
                                    ->deleteAction(
                                        fn ($action) => $action
                                            ->requiresConfirmation()
                                            ->modalHeading('Hapus Lesson?')
                                            ->modalDescription('Lesson akan dihapus permanen.')
                                    ),

                                // QUIZ SECTION (1 per module, optional)
                                Section::make('Kuis Modul')
                                    ->description('Opsional: tambahkan kuis untuk modul ini')
                                    ->schema([
                                        Toggle::make('has_quiz')
                                            ->label('Aktifkan Kuis')
                                            ->helperText('Centang untuk menambahkan kuis pada modul ini')
                                            ->live()
                                            ->default(false)
                                            ->dehydrated(false)
                                            ->afterStateHydrated(function ($state, $set, $record) {
                                                if ($record && $record->quiz) {
                                                    $set('has_quiz', true);
                                                }
                                            }),

                                        Repeater::make('quiz')
                                            ->label('')
                                            ->relationship('quiz')
                                            ->maxItems(1)
                                            ->minItems(0)
                                            ->visible(fn ($get) => $get('has_quiz'))
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Judul Kuis')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder('Masukkan judul kuis')
                                                    ->helperText('Judul kuis untuk modul ini'),

                                                Textarea::make('description')
                                                    ->label('Deskripsi Kuis')
                                                    ->rows(2)
                                                    ->placeholder('Deskripsi singkat tentang kuis ini...')
                                                    ->helperText('Opsional: deskripsi atau instruksi kuis'),

                                                Toggle::make('is_active')
                                                    ->label('Kuis Aktif')
                                                    ->default(true)
                                                    ->helperText('Kuis yang aktif dapat dikerjakan oleh peserta'),

                                                // QUESTIONS REPEATER
                                                Repeater::make('questions')
                                                    ->label('Pertanyaan')
                                                    ->relationship('questions')
                                                    ->collapsible()
                                                    ->minItems(1)
                                                    ->itemLabel(fn (array $state): ?string =>
                                                        Str::limit($state['question'] ?? 'Pertanyaan Baru', 50)
                                                    )
                                                    ->schema([
                                                        Textarea::make('question')
                                                            ->label('Pertanyaan')
                                                            ->required()
                                                            ->rows(2)
                                                            ->placeholder('Tulis pertanyaan di sini...')
                                                            ->helperText('Isi pertanyaan kuis'),

                                                        // ANSWERS REPEATER
                                                        Repeater::make('answers')
                                                            ->label('Pilihan Jawaban')
                                                            ->relationship('answers')
                                                            ->minItems(2)
                                                            ->maxItems(6)
                                                            ->itemLabel(fn (array $state): ?string =>
                                                                ($state['is_correct'] ?? false ? '✓ ' : '') . Str::limit($state['answer'] ?? 'Jawaban', 40)
                                                            )
                                                            ->schema([
                                                                TextInput::make('answer')
                                                                    ->label('Jawaban')
                                                                    ->required()
                                                                    ->maxLength(500)
                                                                    ->placeholder('Tulis pilihan jawaban...')
                                                                    ->helperText('Isi pilihan jawaban'),

                                                                Toggle::make('is_correct')
                                                                    ->label('Jawaban Benar')
                                                                    ->helperText('Tandai sebagai jawaban yang benar')
                                                                    ->inline(false)
                                                                    ->default(false),
                                                            ])
                                                            ->columns(1)
                                                            ->addActionLabel('Tambah Jawaban')
                                                            ->deleteAction(
                                                                fn ($action) => $action
                                                                    ->requiresConfirmation()
                                                                    ->modalHeading('Hapus Jawaban?')
                                                            ),
                                                    ])
                                                    ->columns(1)
                                                    ->addActionLabel('Tambah Pertanyaan')
                                                    ->deleteAction(
                                                        fn ($action) => $action
                                                            ->requiresConfirmation()
                                                            ->modalHeading('Hapus Pertanyaan?')
                                                            ->modalDescription('Pertanyaan beserta semua pilihan jawaban akan dihapus.')
                                                    ),
                                            ])
                                            ->columns(1)
                                            ->addActionLabel('Buat Kuis')
                                            ->deleteAction(
                                                fn ($action) => $action
                                                    ->requiresConfirmation()
                                                    ->modalHeading('Hapus Kuis?')
                                                    ->modalDescription('Kuis beserta semua pertanyaan dan jawaban akan dihapus permanen.')
                                            ),
                                    ])
                                    ->collapsible()
                                    ->collapsed(),
                            ])
                            ->columns(1)
                            ->defaultItems(0)
                            ->addActionLabel('Tambah Modul')
                            ->deleteAction(
                                fn ($action) => $action
                                    ->requiresConfirmation()
                                    ->modalHeading('Hapus Modul?')
                                    ->modalDescription('Modul beserta semua lesson dan kuis di dalamnya akan dihapus permanen.')
                            ),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Status Publikasi')
                    ->description('Atur status kursus')
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Publikasikan Kursus')
                            ->default(false)
                            ->helperText('Kursus yang dipublikasikan akan tampil di halaman publik dan dapat diakses oleh pengguna')
                            ->inline(false),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
