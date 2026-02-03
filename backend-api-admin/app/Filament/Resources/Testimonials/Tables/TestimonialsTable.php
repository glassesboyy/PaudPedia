<?php

namespace App\Filament\Resources\Testimonials\Tables;

use App\Models\Testimonial;
use App\Services\Content\TestimonialService;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo_url')
                    ->label('Foto')
                    ->disk('public')
                    ->circular()
                    ->imageHeight(150)
                    ->defaultImageUrl(url('/images/default-avatar.png')),

                TextColumn::make('display_name')
                    ->label('Nama')
                    ->searchable(['name', 'user.name'])
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Testimonial $record): string => $record->title ?? '-'),

                TextColumn::make('content')
                    ->label('Testimoni')
                    ->limit(50)
                    ->wrap()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state))
                    ->alignCenter()
                    ->sortable(),

                IconColumn::make('is_approved')
                    ->label('Disetujui')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_approved')
                    ->label('Status Persetujuan')
                    ->placeholder('Semua Testimoni')
                    ->trueLabel('Disetujui')
                    ->falseLabel('Belum Disetujui')
                    ->native(false),

                TernaryFilter::make('is_featured')
                    ->label('Status Unggulan')
                    ->placeholder('Semua Testimoni')
                    ->trueLabel('Unggulan')
                    ->falseLabel('Biasa')
                    ->native(false),

                SelectFilter::make('rating')
                    ->label('Rating')
                    ->options([
                        5 => '⭐⭐⭐⭐⭐ 5 Bintang',
                        4 => '⭐⭐⭐⭐ 4 Bintang',
                        3 => '⭐⭐⭐ 3 Bintang',
                        2 => '⭐⭐ 2 Bintang',
                        1 => '⭐ 1 Bintang',
                    ])
                    ->placeholder('Semua Rating'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Lihat'),

                    EditAction::make()
                        ->label('Edit'),

                    Action::make('toggleApproval')
                        ->label(fn (Testimonial $record) => $record->is_approved ? 'Batalkan Persetujuan' : 'Setujui')
                        ->icon(fn (Testimonial $record) => $record->is_approved ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->color(fn (Testimonial $record) => $record->is_approved ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->modalHeading(fn (Testimonial $record) => $record->is_approved ? 'Batalkan Persetujuan Testimoni?' : 'Setujui Testimoni?')
                        ->modalDescription(fn (Testimonial $record) => $record->is_approved 
                            ? ($record->is_featured 
                                ? 'Testimoni akan disembunyikan dari website dan dihapus dari halaman utama (unggulan).' 
                                : 'Testimoni akan disembunyikan dari website.')
                            : 'Testimoni akan ditampilkan di website.')
                        ->action(function (Testimonial $record) {
                            $service = app(TestimonialService::class);
                            
                            try {
                                $wasFeatured = $record->is_featured;
                                $service->toggleApprovalStatus($record);
                                
                                // Custom notification jika featured ikut di-uncheck
                                if (!$record->is_approved && $wasFeatured) {
                                    Notification::make()
                                        ->warning()
                                        ->title('Persetujuan Dibatalkan')
                                        ->body('Testimoni disembunyikan dari website dan dihapus dari halaman utama (unggulan).')
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->success()
                                        ->title($record->is_approved ? 'Testimoni Disetujui' : 'Persetujuan Dibatalkan')
                                        ->body($record->is_approved 
                                            ? 'Testimoni sekarang ditampilkan di website.' 
                                            : 'Testimoni disembunyikan dari website.')
                                        ->send();
                                }
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->danger()
                                    ->title('Gagal Mengubah Status')
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        }),

                    Action::make('toggleFeatured')
                        ->label(fn (Testimonial $record) => $record->is_featured ? 'Batalkan Unggulan' : 'Jadikan Unggulan')
                        ->icon('heroicon-o-star')
                        ->color(fn (Testimonial $record) => $record->is_featured ? 'gray' : 'warning')
                        ->visible(fn (Testimonial $record) => $record->is_approved || $record->is_featured)
                        ->requiresConfirmation()
                        ->modalHeading(fn (Testimonial $record) => $record->is_featured ? 'Batalkan Status Unggulan?' : 'Jadikan Testimoni Unggulan?')
                        ->modalDescription(fn (Testimonial $record) => $record->is_featured 
                            ? 'Testimoni tidak akan ditampilkan di halaman utama.' 
                            : 'Testimoni akan ditampilkan di halaman utama.')
                        ->action(function (Testimonial $record) {
                            // Validasi: tidak bisa dijadikan featured jika belum approved
                            if (!$record->is_featured && !$record->is_approved) {
                                Notification::make()
                                    ->warning()
                                    ->title('Tidak Dapat Dijadikan Unggulan')
                                    ->body('Testimoni harus disetujui terlebih dahulu sebelum dapat dijadikan unggulan.')
                                    ->send();
                                return;
                            }

                            $service = app(TestimonialService::class);
                            
                            try {
                                $service->toggleFeaturedStatus($record);
                                
                                Notification::make()
                                    ->success()
                                    ->title($record->is_featured ? 'Testimoni Unggulan' : 'Status Unggulan Dibatalkan')
                                    ->body($record->is_featured 
                                        ? 'Testimoni ditampilkan di halaman utama.' 
                                        : 'Testimoni dihapus dari halaman utama.')
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->danger()
                                    ->title('Gagal Mengubah Status')
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        }),

                    DeleteAction::make()
                        ->label('Hapus')
                        ->modalHeading('Hapus Testimoni?')
                        ->modalDescription('Data testimoni akan dihapus permanent dan tidak dapat dikembalikan.')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Testimoni berhasil dihapus')
                                ->body('Data testimoni berhasil dihapus.')
                        ),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Aksi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang Dipilih')
                        ->modalHeading('Hapus Testimoni Terpilih?')
                        ->modalDescription('Testimoni yang dipilih akan dihapus permanent dan tidak dapat dikembalikan.')
                        ->successNotificationTitle('Testimoni berhasil dihapus'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum Ada Testimoni')
            ->emptyStateDescription('Mulai tambahkan testimoni dengan klik tombol "Buat Testimoni".')
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right');
    }
}
