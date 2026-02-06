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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                    ->color('success')
                    ->icon('heroicon-m-ticket')
                    ->limit(20),

                TextColumn::make('discount_type')
                    ->label('Tipe')
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
                    ->label('Min. Belanja')
                    ->money('IDR')
                    ->placeholder('-')
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('usage_stats')
                    ->label('Penggunaan')
                    ->formatStateUsing(fn ($record) => 
                        number_format($record->usage_count) . 
                        ($record->usage_limit ? ' / ' . number_format($record->usage_limit) : ' / ∞')
                    )
                    ->badge()
                    ->color(function ($record) {
                        if (!$record->usage_limit) return 'gray';
                        $percentage = ($record->usage_count / $record->usage_limit) * 100;
                        return match(true) {
                            $percentage >= 100 => 'danger',
                            $percentage >= 80 => 'warning',
                            default => 'primary',
                        };
                    })
                    ->alignCenter()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('usage_count', $direction);
                    }),

                TextColumn::make('periode')
                    ->label('Periode')
                    ->formatStateUsing(fn ($record) => 
                        ($record->start_date ? $record->start_date->format('d/m/Y') : '-') . 
                        ' s/d ' . 
                        ($record->end_date ? $record->end_date->format('d/m/Y') : '∞')
                    )
                    ->icon('heroicon-m-calendar')
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(function ($record) {
                        return match(true) {
                            !$record->is_active => 'danger',
                            !$record->isValid() => 'warning',
                            default => 'success',
                        };
                    })
                    ->formatStateUsing(function ($record) {
                        return match(true) {
                            !$record->is_active => 'Nonaktif',
                            $record->start_date && $record->start_date->isFuture() => 'Belum Dimulai',
                            $record->end_date && $record->end_date->isPast() => 'Kadaluarsa',
                            $record->usage_limit && $record->usage_count >= $record->usage_limit => 'Kuota Habis',
                            default => 'Aktif',
                        };
                    })
                    ->alignCenter()
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('is_active', $direction);
                    }),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true)
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
                SelectFilter::make('discount_type')
                    ->label('Tipe Diskon')
                    ->options([
                        DiscountType::PERCENTAGE->value => 'Persentase (%)',
                        DiscountType::FIXED->value => 'Nominal Tetap (Rp)',
                    ])
                    ->placeholder('Semua Tipe')
                    ->native(false),

                TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Status')
                    ->trueLabel('Hanya Aktif')
                    ->falseLabel('Hanya Nonaktif')
                    ->native(false),

                Filter::make('valid_now')
                    ->label('Berlaku Saat Ini')
                    ->query(function (Builder $query): Builder {
                        return $query->where('is_active', true)
                            ->where('start_date', '<=', now())
                            ->where(function($q) {
                                $q->whereNull('end_date')
                                  ->orWhere('end_date', '>=', now());
                            });
                    })
                    ->toggle(),

                Filter::make('expired')
                    ->label('Sudah Kadaluarsa')
                    ->query(function (Builder $query): Builder {
                        return $query->whereNotNull('end_date')
                            ->where('end_date', '<', now());
                    })
                    ->toggle(),

                Filter::make('quota_full')
                    ->label('Kuota Penuh')
                    ->query(function (Builder $query): Builder {
                        return $query->whereNotNull('usage_limit')
                            ->whereRaw('usage_count >= usage_limit');
                    })
                    ->toggle(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Lihat'),

                    EditAction::make()
                        ->label('Edit'),

                    Action::make('toggle_status')
                        ->label(fn ($record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                        ->icon(fn ($record) => $record->is_active ? 'heroicon-m-x-circle' : 'heroicon-m-check-circle')
                        ->color(fn ($record) => $record->is_active ? 'warning' : 'success')
                        ->requiresConfirmation()
                        ->modalHeading(fn ($record) => $record->is_active ? 'Nonaktifkan Kode Promo?' : 'Aktifkan Kode Promo?')
                        ->modalDescription(fn ($record) => $record->is_active 
                            ? 'Kode promo tidak akan bisa digunakan oleh pengguna.' 
                            : 'Kode promo akan aktif dan bisa digunakan oleh pengguna.')
                        ->action(function (PromoCode $record) {
                            $promoCodeService = app(PromoCodeService::class);
                            $promoCodeService->toggleActiveStatus($record);

                            Notification::make()
                                ->success()
                                ->title('Status berhasil diubah')
                                ->body('Kode promo ' . $record->code . ' sekarang ' . ($record->is_active ? 'aktif' : 'nonaktif'))
                                ->send();
                        }),

                    DeleteAction::make()
                        ->label('Hapus')
                        ->modalHeading('Hapus Kode Promo?')
                        ->modalDescription('Data kode promo akan dihapus permanent dan tidak dapat dikembalikan.')
                        ->before(function ($record, $action) {
                            $promoCodeService = app(PromoCodeService::class);
                            
                            if (!$promoCodeService->canBeDeleted($record)) {
                                Notification::make()
                                    ->danger()
                                    ->title('Tidak Dapat Menghapus')
                                    ->body('Kode promo sudah digunakan dalam transaksi dan tidak bisa dihapus.')
                                    ->send();
                                
                                $action->cancel();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Kode promo berhasil dihapus')
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
