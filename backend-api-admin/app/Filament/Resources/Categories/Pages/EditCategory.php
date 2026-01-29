<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Services\Content\CategoryService;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

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
                ->modalHeading('Hapus Kategori?')
                ->modalDescription('Data kategori akan dihapus permanent dan tidak dapat dikembalikan, kategori yang masih terhubung dengan konten lain tidak dapat dihapus.')
                ->before(function ($record, $action) {
                    $categoryService = app(CategoryService::class);
                    if (!$categoryService->canBeDeleted($record)) {
                        Notification::make()
                            ->danger()
                            ->title('Tidak Dapat Menghapus Kategori')
                            ->body($categoryService->getRelatedContentMessage($record))
                            ->send();
                        $action->cancel();
                    }
                })
                ->action(function ($record) {
                    $categoryService = app(CategoryService::class);
                    $categoryService->deleteCategory($record);
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Kategori berhasil dihapus')
                        ->body('Data kategori berhasil dihapus.')
                ),
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $categoryService = app(CategoryService::class);

        $category = $categoryService->updateCategory($record, $data);

        Notification::make()
            ->success()
            ->title('Kategori berhasil diperbarui')
            ->body("Kategori '{$category->name}' telah berhasil diperbarui.");

        return $category;
    }
}
