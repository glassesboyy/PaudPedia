<?php

namespace App\Filament\Resources\Courses\Tables;

use App\Enums\CourseLevel;
use App\Models\Course;
use App\Services\Content\CourseService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_url')
                    ->label('Thumbnail')
                    ->imageHeight(150)
                    ->defaultImageUrl(url('/images/default-course.png')),

                TextColumn::make('title')
                    ->label('Judul Kursus')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->weight('medium')
                    ->wrap(),

                TextColumn::make('mentor.name')
                    ->label('Mentor')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->placeholder('Tanpa Kategori'),

                TextColumn::make('level')
                    ->label('Level')
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label())
                    ->color(fn ($state) => $state?->color()),

                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('discount_percentage')
                    ->label('Diskon')
                    ->getStateUsing(fn (Course $record): ?string =>
                        $record->discount_percentage ? $record->discount_percentage . '%' : null
                    )
                    ->badge()
                    ->color('danger')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('modules_count')
                    ->label('Modul')
                    ->counts('modules')
                    ->badge()
                    ->color('primary')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('enrollments_count')
                    ->label('Pendaftar')
                    ->counts('enrollments')
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-o-users')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_published')
                    ->label('Publikasi')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('mentor_id')
                    ->label('Mentor')
                    ->relationship('mentor', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Mentor'),

                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name', fn ($query) => $query->where('type', 'course'))
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua Kategori'),

                SelectFilter::make('level')
                    ->label('Tingkat Kesulitan')
                    ->options(
                        collect(CourseLevel::cases())
                            ->mapWithKeys(fn (CourseLevel $level) => [$level->value => $level->label()])
                            ->toArray()
                    )
                    ->placeholder('Semua Level')
                    ->native(false),

                TernaryFilter::make('is_published')
                    ->label('Status Publikasi')
                    ->placeholder('Semua Status')
                    ->trueLabel('Dipublikasikan')
                    ->falseLabel('Draft')
                    ->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Lihat'),

                    EditAction::make()
                        ->label('Edit'),

                    Action::make('toggle_publish')
                        ->label(fn (Course $record) => $record->is_published ? 'Unpublish' : 'Publish')
                        ->icon(fn (Course $record) => $record->is_published ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                        ->color(fn (Course $record) => $record->is_published ? 'warning' : 'success')
                        ->requiresConfirmation()
                        ->modalHeading(fn (Course $record) => $record->is_published ? 'Unpublish Kursus?' : 'Publish Kursus?')
                        ->modalDescription(fn (Course $record) => $record->is_published
                            ? 'Kursus tidak akan ditampilkan di halaman publik.'
                            : 'Kursus akan ditampilkan di halaman publik dan dapat diakses pengguna.'
                        )
                        ->action(function (Course $record) {
                            $service = app(CourseService::class);
                            $service->togglePublishStatus($record);

                            Notification::make()
                                ->title($record->is_published ? 'Kursus Dipublikasikan' : 'Kursus Di-unpublish')
                                ->success()
                                ->send();
                        }),

                    DeleteAction::make()
                        ->label('Hapus')
                        ->modalHeading('Hapus Kursus?')
                        ->modalDescription('Kursus akan dihapus sementara dan dapat dipulihkan nanti.')
                        ->successNotificationTitle('Kursus berhasil dihapus'),

                    RestoreAction::make()
                        ->label('Pulihkan')
                        ->successNotificationTitle('Kursus berhasil dipulihkan'),

                    ForceDeleteAction::make()
                        ->label('Hapus Permanen')
                        ->modalHeading('Hapus Permanen?')
                        ->modalDescription('Data kursus beserta semua modul dan lesson akan dihapus permanent dan tidak dapat dikembalikan!')
                        ->successNotificationTitle('Kursus berhasil dihapus permanen'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Aksi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang Dipilih')
                        ->modalHeading('Hapus Kursus Terpilih?')
                        ->modalDescription('Kursus yang dipilih akan dihapus sementara.')
                        ->successNotificationTitle('Kursus berhasil dihapus'),

                    RestoreBulkAction::make()
                        ->label('Pulihkan yang Dipilih')
                        ->successNotificationTitle('Kursus berhasil dipulihkan'),

                    ForceDeleteBulkAction::make()
                        ->label('Hapus Permanen')
                        ->modalHeading('Hapus Permanen?')
                        ->modalDescription('Data akan dihapus permanent dan tidak dapat dikembalikan!')
                        ->successNotificationTitle('Kursus berhasil dihapus permanen'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum ada Kursus')
            ->emptyStateDescription('Buat kursus pertama Anda untuk memulai.')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }
}
