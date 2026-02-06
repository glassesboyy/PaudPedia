<?php

namespace App\Filament\Resources\Schools\Pages;

use App\Filament\Resources\Schools\SchoolResource;
use App\Services\Admin\SchoolService;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSchool extends EditRecord
{
    protected static string $resource = SchoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Lihat')
                ->icon('heroicon-o-eye'),

            DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash')
                ->modalHeading('Hapus Sekolah?')
                ->modalDescription('Data sekolah akan dihapus permanent dan tidak dapat dikembalikan')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Sekolah berhasil dihapus')
                        ->body('Data sekolah berhasil dihapus permanent dan tidak dapat dikembalikan.')
                ),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $schoolService = app(SchoolService::class);
        return $schoolService->updateSchoolSubscription($record, $data);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Sekolah berhasil diperbarui')
            ->body('Perubahan data sekolah telah berhasil disimpan.');
    }
}
