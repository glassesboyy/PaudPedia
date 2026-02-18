<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Lihat')
                ->icon('heroicon-o-eye'),

            DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash')
                ->modalHeading('Hapus Kursus?')
                ->modalDescription('Kursus akan dihapus sementara dan dapat dipulihkan nanti.')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Kursus berhasil dihapus')
                        ->body('Data kursus berhasil dihapus dan dapat dipulihkan nanti.')
                ),

            RestoreAction::make()
                ->label('Pulihkan')
                ->icon('heroicon-o-arrow-uturn-left')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Kursus berhasil dipulihkan')
                        ->body('Data kursus berhasil dipulihkan.')
                ),

            ForceDeleteAction::make()
                ->label('Hapus Permanen')
                ->icon('heroicon-o-trash')
                ->modalHeading('Hapus Permanen?')
                ->modalDescription('Data kursus beserta semua modul dan lesson akan dihapus permanent dan tidak dapat dikembalikan!')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Kursus berhasil dihapus permanen')
                        ->body('Data kursus telah dihapus secara permanen.')
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
            ->title('Kursus berhasil diperbarui')
            ->body('Perubahan data kursus telah disimpan.');
    }
}
