<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PlatformStatsOverview;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\SalesStatsOverview;
use App\Filament\Widgets\UserRegistrationChart;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Gate;
use UnitEnum;

class AnalyticsDashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static string $routePath = 'analytics';
    protected static ?string $title = 'Analytics Dashboard';
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Analytics';
    protected static UnitEnum|string|null $navigationGroup = 'Analitik';
    protected static ?int $navigationSort = 1;

    /**
     * Only admin can access this page
     */
    public static function canAccess(): bool
    {
        return Gate::allows('viewAnalytics');
    }

    /**
     * Define the filters form for date range filtering
     */
    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('period')
                            ->label('Periode')
                            ->options([
                                'today' => 'Hari Ini',
                                'yesterday' => 'Kemarin',
                                'last_7_days' => '7 Hari Terakhir',
                                'last_30_days' => '30 Hari Terakhir',
                                'this_month' => 'Bulan Ini',
                                'last_month' => 'Bulan Lalu',
                                'this_year' => 'Tahun Ini',
                                'last_year' => 'Tahun Lalu',
                                'all_time' => 'Semua Waktu',
                            ])
                            ->default('last_30_days'),
                    ])
                    ->columns(1),
            ]);
    }

    /**
     * Get the widgets to display on the dashboard
     */
    public function getWidgets(): array
    {
        return [
            SalesStatsOverview::class,
            PlatformStatsOverview::class,
            RevenueChart::class,
            UserRegistrationChart::class,
        ];
    }

    /**
     * Set responsive columns for widget grid
     */
    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 4,
            'xl' => 6,
        ];
    }
}
