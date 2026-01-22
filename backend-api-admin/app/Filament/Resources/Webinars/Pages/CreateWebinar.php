<?php

namespace App\Filament\Resources\Webinars\Pages;

use App\Filament\Resources\Webinars\WebinarResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateWebinar extends CreateRecord
{
    protected static string $resource = WebinarResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Webinar berhasil dibuat';
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Webinar Berhasil Dibuat')
            ->body('Webinar "' . $this->record->title . '" telah ditambahkan ke sistem.')
            ->success()
            ->send();
    }
}
