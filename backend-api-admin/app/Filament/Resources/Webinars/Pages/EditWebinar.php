<?php

namespace App\Filament\Resources\Webinars\Pages;

use App\Filament\Resources\Webinars\WebinarResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditWebinar extends EditRecord
{
    protected static string $resource = WebinarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Lihat')
                ->icon('heroicon-o-eye'),
            
            DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash')
                ->modalHeading('Hapus Webinar?')
                ->modalDescription('Webinar akan dihapus sementara dan dapat dipulihkan nanti.')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Webinar berhasil dihapus')
                        ->body('Data webinar berhasil dihapus dan dapat dipulihkan nanti.')
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
            ->title('Webinar berhasil diperbarui')
            ->body('Perubahan data webinar telah disimpan.');
    }
}
