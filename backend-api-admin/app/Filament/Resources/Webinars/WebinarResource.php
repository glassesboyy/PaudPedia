<?php

namespace App\Filament\Resources\Webinars;

use App\Filament\Resources\Webinars\Pages\CreateWebinar;
use App\Filament\Resources\Webinars\Pages\EditWebinar;
use App\Filament\Resources\Webinars\Pages\ListWebinars;
use App\Filament\Resources\Webinars\Pages\ViewWebinar;
use App\Filament\Resources\Webinars\Schemas\WebinarForm;
use App\Filament\Resources\Webinars\Schemas\WebinarInfolist;
use App\Filament\Resources\Webinars\Tables\WebinarsTable;
use App\Models\Webinar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class WebinarResource extends Resource
{
    protected static ?string $model = Webinar::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'Webinar';

    protected static ?string $modelLabel = 'Webinar';

    protected static ?string $pluralModelLabel = 'Webinar';

    protected static string|UnitEnum|null $navigationGroup = 'Konten E-Commerce';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return WebinarForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WebinarInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WebinarsTable::configure($table);
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
            'index' => ListWebinars::route('/'),
            'create' => CreateWebinar::route('/create'),
            'view' => ViewWebinar::route('/{record}'),
            'edit' => EditWebinar::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::upcoming()->active()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
