<?php

namespace App\Filament\Resources\Mentors\Mentors;

use App\Filament\Resources\Mentors\Mentors\Pages\CreateMentor;
use App\Filament\Resources\Mentors\Mentors\Pages\EditMentor;
use App\Filament\Resources\Mentors\Mentors\Pages\ListMentors;
use App\Filament\Resources\Mentors\Mentors\Pages\ViewMentor;
use App\Filament\Resources\Mentors\Mentors\Schemas\MentorForm;
use App\Filament\Resources\Mentors\Mentors\Schemas\MentorInfolist;
use App\Filament\Resources\Mentors\Mentors\Tables\MentorsTable;
use App\Models\Mentors\Mentor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MentorResource extends Resource
{
    protected static ?string $model = Mentor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return MentorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MentorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MentorsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMentors::route('/'),
            'create' => CreateMentor::route('/create'),
            'view' => ViewMentor::route('/{record}'),
            'edit' => EditMentor::route('/{record}/edit'),
        ];
    }
}
