<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Services\Admin\UserService;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Lihat')
                ->icon('heroicon-o-eye'),

            DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash')
                ->modalHeading('Hapus Pengguna?')
                ->modalDescription(
                    'Akun pengguna ini akan dihapus secara permanen dan tidak dapat dipulihkan.'
                )
                ->before(function () {
                    if (Auth::id() === $this->record->id) {
                        Notification::make()
                            ->danger()
                            ->title('Aksi dibatalkan')
                            ->body('Anda tidak dapat menghapus akun Anda sendiri.')
                            ->send();

                        $this->halt();
                    }
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Pengguna berhasil dihapus')
                        ->body('Data pengguna telah dihapus dari sistem.')
                ),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $userService = app(UserService::class);
        return $userService->updateUser($record->id, $data);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pengguna berhasil diperbarui')
            ->body('Perubahan data pengguna telah berhasil disimpan.');
    }
}
