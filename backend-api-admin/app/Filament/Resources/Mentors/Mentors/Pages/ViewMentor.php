<?php

namespace App\Filament\Resources\Mentors\Mentors\Pages;

use App\Filament\Resources\Mentors\Mentors\MentorResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMentor extends ViewRecord
{
    protected static string $resource = MentorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
