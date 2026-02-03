<?php

namespace App\Filament\Resources\Testimonials\Pages;

use App\Filament\Resources\Testimonials\TestimonialResource;
use App\Services\Content\TestimonialService;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTestimonial extends EditRecord
{
    protected static string $resource = TestimonialResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Lihat'),
            DeleteAction::make()
                ->label('Hapus')
                ->modalHeading('Hapus Testimoni')
                ->modalDescription('Apakah Anda yakin ingin menghapus testimoni ini? Data akan dihapus permanent.')
                ->successNotificationTitle('Testimoni berhasil dihapus'),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Testimoni berhasil diperbarui')
            ->body('Perubahan data testimoni telah disimpan.');
    }
}
