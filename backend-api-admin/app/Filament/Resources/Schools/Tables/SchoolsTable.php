<?php

namespace App\Filament\Resources\Schools\Tables;

use App\Enums\SubscriptionPlan;
use App\Models\School;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SchoolsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_url')
                    ->label('Logo')
                    ->disk('public')
                    ->circular()
                    ->imageHeight(50)
                    ->defaultImageUrl(url('/images/default-school.png')),

                TextColumn::make('name')
                    ->label('Nama Sekolah')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50),

                TextColumn::make('npsn')
                    ->label('NPSN')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('NPSN disalin!')
                    ->placeholder('-'),

                TextColumn::make('subscription_plan')
                    ->label('Paket')
                    ->badge()
                    ->color(fn (SubscriptionPlan $state): string => match ($state) {
                        SubscriptionPlan::FREE => 'gray',
                        SubscriptionPlan::PRO => 'success',
                    })
                    ->formatStateUsing(fn (SubscriptionPlan $state): string => $state->label())
                    ->sortable(),

                TextColumn::make('headmaster_name')
                    ->label('Kepala Sekolah')
                    ->searchable()
                    ->icon('heroicon-m-user-circle')
                    ->placeholder('Belum ada')
                    ->limit(30)
                    ->tooltip(fn ($state) => $state),

                TextColumn::make('total_students')
                    ->label('Total Siswa')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-o-academic-cap')
                    ->alignCenter()
                    ->placeholder('0')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_teachers')
                    ->label('Total Guru')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-users')
                    ->alignCenter()
                    ->placeholder('0')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('total_classes')
                    ->label('Total Kelas')
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-o-building-library')
                    ->alignCenter()
                    ->placeholder('0')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->toggleable()
                    ->icon('heroicon-m-phone')
                    ->placeholder('-'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable()
                    ->icon('heroicon-m-envelope')
                    ->copyable()
                    ->copyMessage('Email disalin!')
                    ->placeholder('-'),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('subscription_ended_at')
                    ->label('Berakhir')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable()
                    ->badge()
                    ->color(function ($state) {
                        if (!$state) return 'success';
                        if ($state->isPast()) return 'danger';
                        if ($state->diffInDays(now()) <= 7) return 'warning';
                        return 'success';
                    })
                    ->icon(function ($state) {
                        if (!$state) return 'heroicon-o-check-badge';
                        if ($state->isPast()) return 'heroicon-o-x-circle';
                        if ($state->diffInDays(now()) <= 7) return 'heroicon-o-exclamation-circle';
                        return 'heroicon-o-check-badge';
                    })
                    ->placeholder('Tidak terbatas'),

                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('subscription_plan')
                    ->label('Paket Berlangganan')
                    ->options([
                        SubscriptionPlan::FREE->value => SubscriptionPlan::FREE->label(),
                        SubscriptionPlan::PRO->value => SubscriptionPlan::PRO->label(),
                    ])
                    ->placeholder('Semua Paket')
                    ->native(false),

                TernaryFilter::make('is_active')
                    ->label('Status Sekolah')
                    ->placeholder('Semua sekolah')
                    ->trueLabel('Hanya aktif')
                    ->falseLabel('Hanya nonaktif')
                    ->native(false),

                Filter::make('subscription_ended_at')
                    ->schema([
                        DatePicker::make('subscription_from')
                            ->label('Berakhir dari')
                            ->native(false),
                        DatePicker::make('subscription_until')
                            ->label('Berakhir sampai')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['subscription_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('subscription_ended_at', '>=', $date),
                            )
                            ->when(
                                $data['subscription_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('subscription_ended_at', '<=', $date),
                            );
                    }),

                Filter::make('created_at')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label('Terdaftar dari')
                            ->native(false),
                        DatePicker::make('created_until')
                            ->label('Terdaftar sampai')
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
                    ViewAction::make()
                        ->label('Lihat'),
                    
                    EditAction::make()
                        ->label('Edit'),

                    DeleteAction::make()
                        ->label('Hapus')
                        ->modalHeading('Hapus Sekolah?')
                        ->modalDescription('Data sekolah akan dihapus permanent dan tidak dapat dikembalikan.')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Sekolah dihapus')
                                ->body('Sekolah berhasil dihapus.'),
                        ),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Aksi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang Dipilih')
                        ->modalHeading('Hapus Sekolah Terpilih?')
                        ->modalDescription('Data sekolah yang dipilih akan dihapus permanent dan tidak dapat dikembalikan.')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Sekolah dihapus')
                                ->body('Sekolah yang dipilih berhasil dihapus.'),
                        ),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum ada Sekolah')
            ->emptyStateDescription('Tambahkan sekolah pertama untuk memulai.')
            ->emptyStateIcon('heroicon-o-building-office-2');
    }
}
