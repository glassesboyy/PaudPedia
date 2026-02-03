<?php

namespace App\Filament\Resources\Mentors\Pages;

use App\Filament\Resources\Mentors\MentorResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditMentor extends EditRecord
{
    protected static string $resource = MentorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Lihat')
                ->icon('heroicon-o-eye'),

            DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash')
                ->modalHeading('Hapus Mentor?')
                ->modalDescription('Data mentor akan dihapus permanent dan tidak dapat dikembalikan')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Mentor berhasil dihapus')
                        ->body('Data mentor berhasil dihapus permanent dan tidak dapat dikembalikan.')
                ),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Mentor berhasil diperbarui')
            ->body('Perubahan data mentor telah disimpan.');
    }
}
