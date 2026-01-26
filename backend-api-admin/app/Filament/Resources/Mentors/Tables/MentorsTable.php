<?php

namespace App\Filament\Resources\Mentors\Tables;

use App\Models\Mentor;
use App\Services\Content\MentorService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MentorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_url')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),

                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Mentor $record): string => $record->title ?? '-'),

                TextColumn::make('expertise')
                    ->label('Keahlian')
                    ->searchable()
                    ->badge()
                    ->color('warning')
                    ->placeholder('-')
                    ->limit(30),

                TextColumn::make('total_webinars')
                    ->label('Webinar')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-video-camera')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('total_courses')
                    ->label('Kursus')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-academic-cap')
                    ->alignCenter()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->sortable(),

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
                TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif')
                    ->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Lihat'),
                    
                    EditAction::make()
                        ->label('Edit'),
                    
                    Action::make('toggle_active')
                        ->label(fn (Mentor $record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                        ->icon(fn (Mentor $record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                        ->color(fn (Mentor $record) => $record->is_active ? 'warning' : 'success')
                        ->requiresConfirmation()
                        ->modalHeading(fn (Mentor $record) => $record->is_active ? 'Nonaktifkan Mentor?' : 'Aktifkan Mentor?')
                        ->modalDescription(function (Mentor $record) {
                            if ($record->is_active) {
                                $service = app(MentorService::class);
                                $counts = $service->getRelatedContentCount($record);
                                
                                if ($counts['total'] > 0) {
                                    return 'Mentor ini masih terhubung dengan ' . 
                                           ($counts['webinars'] > 0 ? "{$counts['webinars']} webinar" : '') .
                                           ($counts['webinars'] > 0 && $counts['courses'] > 0 ? ' dan ' : '') .
                                           ($counts['courses'] > 0 ? "{$counts['courses']} kursus" : '') .
                                           '. Apakah Anda yakin ingin menonaktifkannya?';
                                }
                                
                                return 'Mentor yang tidak aktif tidak akan ditampilkan di halaman publik.';
                            }
                            
                            return 'Mentor akan diaktifkan dan ditampilkan di halaman publik.';
                        })
                        ->action(function (Mentor $record) {
                            $service = app(MentorService::class);
                            
                            try {
                                $service->toggleActiveStatus($record);
                                
                                Notification::make()
                                    ->title($record->is_active ? 'Mentor Diaktifkan' : 'Mentor Dinonaktifkan')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Gagal Mengubah Status')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    
                    DeleteAction::make()
                        ->label('Hapus')
                        ->modalHeading('Hapus Mentor?')
                        ->modalDescription('Data mentor akan dihapus permanent dan tidak dapat dikembalikan.')
                        ->before(function (Mentor $record, DeleteAction $action) {
                            $service = app(MentorService::class);
                            
                            if (!$service->canBeDeleted($record)) {
                                Notification::make()
                                    ->title('Tidak Dapat Menghapus Mentor')
                                    ->body($service->getRelatedContentMessage($record, 'delete'))
                                    ->danger()
                                    ->send();
                                
                                $action->cancel();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Mentor dihapus')
                                ->body('Data mentor berhasil dihapus.'),
                        ),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Aksi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang Dipilih')
                        ->modalHeading('Hapus Mentor Terpilih?')
                        ->modalDescription('Mentor yang dipilih akan dihapus permanent dan tidak dapat dikembalikan.')
                        ->before(function (DeleteBulkAction $action, $records) {
                            $service = app(MentorService::class);
                            $blockedMentors = [];
                            
                            foreach ($records as $record) {
                                if (!$service->canBeDeleted($record)) {
                                    $blockedMentors[] = $record->name;
                                }
                            }
                            
                            if (!empty($blockedMentors)) {
                                Notification::make()
                                    ->title('Tidak Dapat Menghapus Beberapa Mentor')
                                    ->body('Mentor berikut masih terhubung dengan webinar atau kursus: ' . implode(', ', $blockedMentors))
                                    ->danger()
                                    ->persistent()
                                    ->send();
                                
                                $action->cancel();
                            }
                        })
                        ->successNotificationTitle('Mentor berhasil dihapus'),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Mentor')
            ->emptyStateDescription('Mulai tambahkan mentor dengan klik tombol "Buat Mentor".')
            ->emptyStateIcon('heroicon-o-users')
            ->defaultSort('created_at', 'desc');
    }
}
