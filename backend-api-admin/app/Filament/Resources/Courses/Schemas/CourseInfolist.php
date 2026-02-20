<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Enums\ContentType;
use App\Models\Course;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class CourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kursus')
                    ->schema([
                        ImageEntry::make('thumbnail_url')
                            ->label('Thumbnail')
                            ->disk('public')
                            ->defaultImageUrl(url('/images/default-course.png')),

                        TextEntry::make('title')
                            ->label('Judul')
                            ->size('large')
                            ->weight(FontWeight::Bold)
                            ->copyable()
                            ->columnSpanFull(),

                        TextEntry::make('slug')
                            ->label('Slug')
                            ->copyable()
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('mentor.name')
                            ->label('Mentor')
                            ->badge()
                            ->color('info')
                            ->placeholder('Tidak ada Mentor'),

                        TextEntry::make('category.name')
                            ->label('Kategori')
                            ->badge()
                            ->color('success')
                            ->placeholder('Tidak ada Kategori'),

                        TextEntry::make('level')
                            ->label('Tingkat Kesulitan')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state?->label())
                            ->color(fn ($state) => $state?->color()),

                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->html()
                            ->placeholder('Tidak ada deskripsi'),
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
                            ->getStateUsing(fn (Course $record): ?string =>
                                $record->discount_percentage ? $record->discount_percentage . '%' : null
                            )
                            ->badge()
                            ->color('danger')
                            ->placeholder('-'),

                        TextEntry::make('duration_hours')
                            ->label('Estimasi Durasi')
                            ->suffix(' jam')
                            ->placeholder('-'),

                        TextEntry::make('total_enrollments')
                            ->label('Total Pendaftar')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('completion_rate')
                            ->label('Tingkat Penyelesaian')
                            ->getStateUsing(fn (Course $record): string =>
                                $record->completion_rate . '%'
                            )
                            ->badge()
                            ->color('info'),

                        IconEntry::make('is_published')
                            ->label('Status Publikasi')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Modul & Lesson')
                    ->schema([
                        RepeatableEntry::make('modules')
                            ->label('')
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Judul Modul')
                                    ->weight(FontWeight::Bold)
                                    ->size('medium'),

                                TextEntry::make('description')
                                    ->label('Deskripsi')
                                    ->placeholder('Tidak ada deskripsi'),

                                TextEntry::make('total_lessons')
                                    ->label('Jumlah Lesson')
                                    ->badge()
                                    ->color('primary'),

                                RepeatableEntry::make('lessons')
                                    ->label('Lesson')
                                    ->schema([
                                        TextEntry::make('title')
                                            ->label('Judul Lesson'),

                                        TextEntry::make('content_type')
                                            ->label('Tipe Konten')
                                            ->badge()
                                            ->formatStateUsing(fn ($state) => $state instanceof ContentType ? $state->label() : $state)
                                            ->color(fn ($state) => $state instanceof ContentType ? $state->color() : 'gray'),

                                        // Video URL - untuk tipe VIDEO
                                        TextEntry::make('video_url')
                                            ->label('URL Video')
                                            ->url(fn ($state) => $state, shouldOpenInNewTab: true)
                                            ->placeholder('-')
                                            ->color('primary')
                                            ->visible(fn ($record) => $record->content_type === ContentType::VIDEO),

                                        // PDF File - untuk tipe PDF
                                        TextEntry::make('pdf_file')
                                            ->label('File PDF')
                                            ->url(fn ($state) => $state ? asset('storage/' . $state) : null, shouldOpenInNewTab: true)
                                            ->placeholder('-')
                                            ->color('primary')
                                            ->visible(fn ($record) => $record->content_type === ContentType::PDF),

                                        // Text Content - untuk tipe TEXT
                                        TextEntry::make('text_content')
                                            ->label('Konten Teks')
                                            ->html()
                                            ->placeholder('-')
                                            ->visible(fn ($record) => $record->content_type === ContentType::TEXT),

                                        TextEntry::make('duration_minutes')
                                            ->label('Durasi')
                                            ->suffix(' menit')
                                            ->placeholder('-'),
                                    ])
                                    ->columns(1),

                                // Quiz Section
                                Section::make('Quiz')
                                    ->schema([
                                        RepeatableEntry::make('quiz')
                                            ->label('')
                                            ->schema([
                                                TextEntry::make('title')
                                                    ->label('Judul Quiz')
                                                    ->weight(FontWeight::Bold),

                                                TextEntry::make('description')
                                                    ->label('Deskripsi Quiz')
                                                    ->placeholder('Tidak ada deskripsi'),

                                                RepeatableEntry::make('questions')
                                                    ->label('Pertanyaan')
                                                    ->schema([
                                                        TextEntry::make('question')
                                                            ->label('Pertanyaan')
                                                            ->weight(FontWeight::Medium),

                                                        RepeatableEntry::make('answers')
                                                            ->label('Pilihan Jawaban')
                                                            ->schema([
                                                                TextEntry::make('answer')
                                                                    ->label('')
                                                                    ->formatStateUsing(fn ($state, $record) =>
                                                                        $record->is_correct
                                                                            ? "✓ {$state}"
                                                                            : "○ {$state}"
                                                                    )
                                                                    ->color(fn ($record) => $record->is_correct ? 'success' : 'gray'),
                                                            ])
                                                            ->columns(1),
                                                    ])
                                                    ->columns(1),
                                            ])
                                            ->columns(1),
                                    ])
                                    ->visible(fn ($record) => $record->quiz->isNotEmpty())
                                    ->collapsible()
                                    ->collapsed(),
                            ])
                            ->columns(1),
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
                            ->visible(fn (Course $record): bool => $record->trashed()),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
