<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Enums\OrderStatus;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('No. Order')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Nomor Order disalin!'),

                TextColumn::make('user.name')
                    ->label('Pembeli')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user'),

                TextColumn::make('final_amount')
                    ->label('Total Pembayaran')
                    ->money('idr')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (OrderStatus $state): string => match ($state) {
                        OrderStatus::PENDING => 'warning',
                        OrderStatus::PAID => 'success',
                        OrderStatus::CANCELLED => 'danger',
                        OrderStatus::EXPIRED => 'danger',
                    })
                    ->formatStateUsing(fn (OrderStatus $state): string => $state->label())
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Metode Bayar')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('paid_at')
                    ->label('Tgl Bayar')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Belum dibayar'),

                TextColumn::make('created_at')
                    ->label('Tgl Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pembayaran')
                    ->options([
                        OrderStatus::PENDING->value => OrderStatus::PENDING->label(),
                        OrderStatus::PAID->value => OrderStatus::PAID->label(),
                        OrderStatus::CANCELLED->value => OrderStatus::CANCELLED->label(),
                        OrderStatus::EXPIRED->value => OrderStatus::EXPIRED->label(),
                    ])
                    ->native(false),

                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label('Dibuat dari')
                            ->native(false),
                        DatePicker::make('created_until')
                            ->label('Dibuat sampai')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->label('Detail'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Aksi'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
