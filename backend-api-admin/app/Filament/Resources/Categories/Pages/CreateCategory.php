<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Services\Content\CategoryService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $categoryService = app(CategoryService::class);

        $category = $categoryService->createCategory($data);

        Notification::make()
            ->success()
            ->title('Kategori berhasil dibuat')
            ->body("Kategori '{$category->name}' telah berhasil ditambahkan.")
            ->send();

        return $category;
    }
}
