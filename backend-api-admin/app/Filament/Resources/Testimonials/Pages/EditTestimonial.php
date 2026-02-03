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
                ->label('Lihat')
                ->icon('heroicon-o-eye'),

            DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash')
                ->modalHeading('Hapus Testimoni?')
                ->modalDescription('Data testimoni akan dihapus permanent dan tidak dapat dikembalikan')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Testimoni berhasil dihapus')
                        ->body('Data testimoni berhasil dihapus permanent dan tidak dapat dikembalikan.')
                ),
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
