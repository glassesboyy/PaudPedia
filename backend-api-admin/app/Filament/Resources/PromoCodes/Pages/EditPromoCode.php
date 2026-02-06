<?php

namespace App\Filament\Resources\PromoCodes\Pages;

use App\Filament\Resources\PromoCodes\PromoCodeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPromoCode extends EditRecord
{
    protected static string $resource = PromoCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Lihat'),
            DeleteAction::make()
                ->label('Hapus')
                ->modalHeading('Hapus Kode Promo?')
                ->modalDescription('Data kode promo akan dihapus permanent dan tidak dapat dikembalikan.')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Kode Promo Berhasil Dihapus')
                        ->body('Data kode promo berhasil dihapus.')
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
            ->title('Kode Promo Berhasil Diperbarui')
            ->body('Perubahan kode promo berhasil disimpan.');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure code is uppercase
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        return $data;
    }
}
