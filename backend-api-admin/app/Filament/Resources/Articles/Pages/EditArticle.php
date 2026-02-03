<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Filament\Resources\Articles\ArticleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Lihat')
                ->icon('heroicon-o-eye'),
            
            DeleteAction::make()
                ->label('Hapus')
                ->icon('heroicon-o-trash')
                ->modalHeading('Hapus Artikel?')
                ->modalDescription('Artikel akan dihapus sementara dan dapat dipulihkan nanti.')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Artikel berhasil dihapus')
                        ->body('Data artikel berhasil dihapus dan tidak dapat dipulihkan.')
                ),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Karena field author_id sudah tidak ada di form,
        unset($data['author_id']);
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Artikel berhasil diperbarui')
            ->body('Perubahan data artikel telah disimpan.');
    }
}
