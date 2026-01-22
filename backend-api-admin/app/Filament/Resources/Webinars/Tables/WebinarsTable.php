<?php

namespace App\Filament\Resources\Webinars\Tables;

use App\Models\Webinar;
use App\Services\Content\WebinarService;
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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WebinarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_url')
                    ->label('Thumbnail')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-webinar.png')),

                TextColumn::make('title')
                    ->label('Judul Webinar')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn (Webinar $record): string => $record->title)
                    ->weight('medium'),

                TextColumn::make('mentor.name')
                    ->label('Mentor')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('scheduled_at')
                    ->label('Jadwal')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->timezone('Asia/Jakarta')
                    ->badge()
                    ->color(fn (Webinar $record): string => 
                        $record->isUpcoming() ? 'success' : 'gray'
                    )
                    ->icon(fn (Webinar $record): string => 
                        $record->isUpcoming() ? 'heroicon-o-calendar' : 'heroicon-o-clock'
                    ),

                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('discount_percentage')
                    ->label('Diskon')
                    ->getStateUsing(fn (Webinar $record): ?string => 
                        $record->discount_percentage ? $record->discount_percentage . '%' : null
                    )
                    ->badge()
                    ->color('warning')
                    ->placeholder('-'),

                TextColumn::make('total_purchases')
                    ->label('Terjual')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-shopping-cart')
                    ->alignCenter(),

                IconColumn::make('is_active')
                    ->label('Status')
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

                TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif')
                    ->native(false),

                SelectFilter::make('schedule_status')
                    ->label('Status Jadwal')
                    ->options([
                        'upcoming' => 'Webinar Mendatang',
                        'past' => 'Webinar Selesai',
                    ])
                    ->placeholder('Semua Jadwal')
                    ->query(function (Builder $query, array $data) {
                        return match ($data['value'] ?? null) {
                            'upcoming' => $query->upcoming(),
                            'past' => $query->past(),
                            default => $query,
                        };
                    })
                    ->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Lihat'),
                    
                    EditAction::make()
                        ->label('Edit'),
                    
                    Action::make('toggle_active')
                        ->label(fn (Webinar $record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                        ->icon(fn (Webinar $record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                        ->color(fn (Webinar $record) => $record->is_active ? 'warning' : 'success')
                        ->requiresConfirmation()
                        ->modalHeading(fn (Webinar $record) => $record->is_active ? 'Nonaktifkan Webinar?' : 'Aktifkan Webinar?')
                        ->modalDescription('Webinar yang tidak aktif tidak akan ditampilkan di halaman publik.')
                        ->action(function (Webinar $record) {
                            $service = app(WebinarService::class);
                            $service->toggleActiveStatus($record);
                            
                            Notification::make()
                                ->title($record->is_active ? 'Webinar Diaktifkan' : 'Webinar Dinonaktifkan')
                                ->success()
                                ->send();
                        }),
                    
                    DeleteAction::make()
                        ->label('Hapus')
                        ->modalHeading('Hapus Webinar?')
                        ->modalDescription('Webinar akan dihapus sementara dan dapat dipulihkan nanti.')
                        ->successNotificationTitle('Webinar berhasil dihapus'),
                    
                    RestoreAction::make()
                        ->label('Pulihkan')
                        ->successNotificationTitle('Webinar berhasil dipulihkan'),
                    
                    ForceDeleteAction::make()
                        ->label('Hapus Permanen')
                        ->modalHeading('Hapus Permanen?')
                        ->modalDescription('Data akan dihapus permanent dan tidak dapat dikembalikan!')
                        ->successNotificationTitle('Webinar berhasil dihapus permanen'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Aksi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang Dipilih')
                        ->modalHeading('Hapus Webinar Terpilih?')
                        ->modalDescription('Webinar yang dipilih akan dihapus sementara.')
                        ->successNotificationTitle('Webinar berhasil dihapus'),
                    
                    RestoreBulkAction::make()
                        ->label('Pulihkan yang Dipilih')
                        ->successNotificationTitle('Webinar berhasil dipulihkan'),
                    
                    ForceDeleteBulkAction::make()
                        ->label('Hapus Permanen')
                        ->modalHeading('Hapus Permanen?')
                        ->modalDescription('Data akan dihapus permanent dan tidak dapat dikembalikan!')
                        ->successNotificationTitle('Webinar berhasil dihapus permanen'),
                ]),
            ])
            ->defaultSort('scheduled_at', 'desc')
            ->emptyStateHeading('Belum ada Webinar')
            ->emptyStateDescription('Buat webinar pertama Anda untuk memulai.')
            ->emptyStateIcon('heroicon-o-video-camera');
    }
}
