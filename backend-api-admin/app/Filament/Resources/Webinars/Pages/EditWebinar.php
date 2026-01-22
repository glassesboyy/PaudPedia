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
                ->label('Lihat'),
            
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
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Webinar berhasil diperbarui';
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Webinar Berhasil Diperbarui')
            ->body('Perubahan pada webinar "' . $this->record->title . '" telah disimpan.')
            ->success()
            ->send();
    }
}
