<?php

namespace App\Filament\Resources\PromoCodes\Tables;

use App\Enums\DiscountType;
use App\Models\PromoCode;
use App\Services\Content\PromoCodeService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PromoCodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode Promo')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Kode promo disalin!')
                    ->badge()
                    ->color('success'),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable()
                    ->limit(50)
                    ->placeholder('-')
                    ->wrap(),

                TextColumn::make('discount_type')
                    ->label('Tipe Diskon')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        DiscountType::PERCENTAGE => 'info',
                        DiscountType::FIXED => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state instanceof DiscountType ? $state->label() : $state)
                    ->sortable(),

                TextColumn::make('discount_value')
                    ->label('Nilai Diskon')
                    ->formatStateUsing(fn ($state, $record) => 
                        $record->discount_type === DiscountType::PERCENTAGE 
                            ? $state . '%' 
                            : 'Rp ' . number_format($state, 0, ',', '.')
                    )
                    ->weight('medium')
                    ->color('success')
                    ->sortable(),

                TextColumn::make('min_purchase_amount')
                    ->label('Min. Pembelian')
                    ->formatStateUsing(fn ($state) => 
                        $state ? 'Rp ' . number_format($state, 0, ',', '.') : '-'
                    )
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('max_discount_amount')
                    ->label('Max. Diskon')
                    ->formatStateUsing(fn ($state) => 
                        $state ? 'Rp ' . number_format($state, 0, ',', '.') : '-'
                    )
                    ->placeholder('-')
                    ->toggleable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('usage_limit')
                    ->label('Batas Penggunaan')
                    ->formatStateUsing(fn ($state) => 
                        $state ? number_format($state) : 'Unlimited'
                    )
                    ->badge()
                    ->color(fn ($state) => $state ? 'warning' : 'success')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('usage_count')
                    ->label('Sudah Digunakan')
                    ->formatStateUsing(fn ($state) => number_format($state) . 'x')
                    ->badge()
                    ->color('primary')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('start_date')
                    ->label('Mulai')
                    ->dateTime('d M Y')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('end_date')
                    ->label('Berakhir')
                    ->dateTime('d M Y')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('discount_type')
                    ->label('Tipe Diskon')
                    ->options([
                        DiscountType::PERCENTAGE->value => 'Persentase (%)',
                        DiscountType::FIXED->value => 'Fixed (Rp)',
                    ])
                    ->placeholder('Semua Tipe')
                    ->native(false),

                TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Status')
                    ->trueLabel('Hanya Aktif')
                    ->falseLabel('Hanya Nonaktif')
                    ->native(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Lihat'),

                    EditAction::make()
                        ->label('Edit'),

                    Action::make('toggle_status')
                        ->label(fn (PromoCode $record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                        ->icon(fn (PromoCode $record) => $record->is_active ? 'heroicon-m-x-circle' : 'heroicon-m-check-circle')
                        ->color(fn (PromoCode $record) => $record->is_active ? 'warning' : 'success')
                        ->requiresConfirmation()
                        ->modalHeading(fn (PromoCode $record) => $record->is_active ? 'Nonaktifkan Kode Promo?' : 'Aktifkan Kode Promo?')
                        ->modalDescription(fn (PromoCode $record) => $record->is_active 
                            ? 'Kode promo tidak akan bisa digunakan oleh pengguna setelah dinonaktifkan.' 
                            : 'Kode promo akan aktif dan bisa digunakan oleh pengguna.')
                        ->modalSubmitActionLabel(fn (PromoCode $record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                        ->action(function (PromoCode $record) {
                            $promoCodeService = app(PromoCodeService::class);
                            $promoCodeService->toggleActiveStatus($record);

                            Notification::make()
                                ->success()
                                ->title('Status Berhasil Diubah')
                                ->body('Kode promo "' . $record->code . '" sekarang ' . ($record->is_active ? 'aktif' : 'nonaktif') . '.')
                                ->send();
                        }),

                    DeleteAction::make()
                        ->label('Hapus')
                        ->modalHeading('Hapus Kode Promo?')
                        ->modalDescription('Data kode promo akan dihapus permanent dan tidak dapat dikembalikan.')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Kode Promo Berhasil Dihapus')
                                ->body('Data kode promo berhasil dihapus.')
                        ),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip('Aksi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang Dipilih')
                        ->modalHeading('Hapus Kode Promo Terpilih?')
                        ->modalDescription('Kode promo yang dipilih akan dihapus permanent.')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Kode promo berhasil dihapus')
                                ->body('Kode promo yang dipilih berhasil dihapus.')
                        ),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum ada Kode Promo')
            ->emptyStateDescription('Tambahkan kode promo pertama untuk memberikan diskon kepada pelanggan.')
            ->emptyStateIcon('heroicon-o-ticket');
    }
}
