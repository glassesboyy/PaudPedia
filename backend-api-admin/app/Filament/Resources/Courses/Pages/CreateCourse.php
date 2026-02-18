<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use App\Services\Content\CourseService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCourse extends CreateRecord
{
    protected static string $resource = CourseResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Kursus berhasil dibuat')
            ->body('Kursus baru telah ditambahkan.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $service = app(CourseService::class);

        // Generate unique slug jika kosong
        if (empty($data['slug'])) {
            $data['slug'] = $service->generateUniqueSlug($data['title']);
        }

        return $data;
    }
}
