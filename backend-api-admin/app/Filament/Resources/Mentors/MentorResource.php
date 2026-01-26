<?php

namespace App\Filament\Resources\Mentors;

use App\Filament\Resources\Mentors\Pages\CreateMentor;
use App\Filament\Resources\Mentors\Pages\EditMentor;
use App\Filament\Resources\Mentors\Pages\ListMentors;
use App\Filament\Resources\Mentors\Pages\ViewMentor;
use App\Filament\Resources\Mentors\Schemas\MentorForm;
use App\Filament\Resources\Mentors\Schemas\MentorInfolist;
use App\Filament\Resources\Mentors\Tables\MentorsTable;
use App\Models\Mentor;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MentorResource extends Resource
{
    protected static ?string $model = Mentor::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Mentor';

    protected static ?string $modelLabel = 'Mentor';

    protected static ?string $pluralModelLabel = 'Mentor';

    protected static string|UnitEnum|null $navigationGroup = 'Konten E-Commerce';

    protected static ?int $navigationSort = 3;

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

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::active()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
