<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use App\Services\Admin\UserService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->disk('public')
                    ->circular()
                    ->imageHeight(150)
                    ->defaultImageUrl(url('/images/default-avatar.png')),

                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-envelope')
                    ->copyable()
                    ->copyMessage('Email disalin!'),

                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color('primary')
                    ->separator(', ')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Tidak ada role'),

                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('-'),

                TextColumn::make('email_verified_at')
                    ->label('Email Terverifikasi')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable()
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'warning')
                    ->formatStateUsing(fn ($state) => $state ? 'Terverifikasi' : 'Belum')
                    ->icon(fn ($state) => $state ? 'heroicon-m-check-badge' : 'heroicon-m-clock'),

                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Aktif' : 'Nonaktif')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Akun')
                    ->placeholder('Semua pengguna')
                    ->trueLabel('Hanya aktif')
                    ->falseLabel('Hanya nonaktif')
                    ->native(false),

                TernaryFilter::make('email_verified_at')
                    ->label('Email Terverifikasi')
                    ->placeholder('Semua pengguna')
                    ->trueLabel('Sudah terverifikasi')
                    ->falseLabel('Belum terverifikasi')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('email_verified_at'),
                        false: fn (Builder $query) => $query->whereNull('email_verified_at'),
                    )
                    ->native(false),

                SelectFilter::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->native(false),

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

                    Action::make('toggleActive')
                        ->label(fn (User $record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                        ->icon(fn (User $record) => $record->is_active ? 'heroicon-m-no-symbol' : 'heroicon-m-check-circle')
                        ->color(fn (User $record) => $record->is_active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->modalHeading(fn (User $record) => $record->is_active ? 'Nonaktifkan Pengguna?' : 'Aktifkan Pengguna?')
                        ->modalDescription(fn (User $record) => $record->is_active 
                            ? 'Pengguna tidak akan bisa login setelah dinonaktifkan.'
                            : 'Pengguna akan dapat login kembali setelah diaktifkan.')
                        ->modalSubmitActionLabel(fn (User $record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                        ->action(function (User $record) {
                            $userService = app(UserService::class);
                            $newStatus = !$record->is_active;
                            
                            $userService->toggleActiveStatus($record->id);
                            
                            Notification::make()
                                ->title('Status pengguna berhasil diperbarui')
                                ->body("Pengguna {$record->name} telah " . ($newStatus ? 'diaktifkan' : 'dinonaktifkan'))
                                ->success()
                                ->send();
                        }),

                    DeleteAction::make()
                        ->label('Hapus')
                        ->before(function (User $record) {
                            // Prevent deleting yourself
                            $currentUserId = Auth::id();
                            if ($currentUserId === $record->id) {
                                Notification::make()
                                    ->title('Tidak dapat menghapus pengguna')
                                    ->body('Anda tidak dapat menghapus akun Anda sendiri.')
                                    ->danger()
                                    ->send();
                                    
                                return false;
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Pengguna dihapus')
                                ->body('Pengguna berhasil dihapus dari sistem.'),
                        ),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Aksi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus yang Dipilih')
                        ->before(function ($records) {
                            // Prevent deleting yourself
                            $currentUserId = Auth::id();
                            if ($records->contains('id', $currentUserId)) {
                                Notification::make()
                                    ->title('Tidak dapat menghapus pengguna')
                                    ->body('Anda tidak dapat menghapus akun Anda sendiri.')
                                    ->danger()
                                    ->send();
                                    
                                return false;
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Pengguna dihapus')
                                ->body('Pengguna yang dipilih berhasil dihapus.'),
                        ),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum ada Pengguna')
            ->emptyStateDescription('Tambahkan pengguna pertama untuk memulai.')
            ->emptyStateIcon('heroicon-o-users');
    }
}
