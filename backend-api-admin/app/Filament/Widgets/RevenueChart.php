<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class RevenueChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Grafik Pendapatan';
    protected ?string $pollingInterval = '60s';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = [
        'default' => 'full',
    ];

    protected ?string $maxHeight = '320px';
    protected string $color = 'success';

    public ?string $filter = 'daily';

    /**
     * Only admin can view this widget
     */
    public static function canView(): bool
    {
        return Gate::allows('viewAnalytics');
    }

    protected function getFilters(): ?array
    {
        return [
            'daily' => 'Harian',
            'weekly' => 'Mingguan',
            'monthly' => 'Bulanan',
        ];
    }

    protected function getData(): array
    {
        [$startDate, $endDate] = $this->getDateRange();
        $data = $this->getRevenueData($startDate, $endDate);

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $data->pluck('revenue')->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Jumlah Transaksi',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'fill' => false,
                    'tension' => 0.3,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $data->pluck('label')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Pendapatan (Rp)',
                    ],
                    'ticks' => [
                        'callback' => "function(value) { return 'Rp ' + value.toLocaleString('id-ID'); }",
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Transaksi',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
        ];
    }

    /**
     * Get revenue data grouped by selected filter
     */
    protected function getRevenueData(?string $startDate, ?string $endDate): Collection
    {
        $filter = $this->filter;

        return match ($filter) {
            'weekly' => $this->getWeeklyData($startDate, $endDate),
            'monthly' => $this->getMonthlyData($startDate, $endDate),
            default => $this->getDailyData($startDate, $endDate),
        };
    }

    /**
     * Get daily revenue data
     */
    protected function getDailyData(?string $startDate, ?string $endDate): Collection
    {
        $start = Carbon::parse($startDate ?? now()->subDays(29));
        $end = Carbon::parse($endDate ?? now());
        $days = $start->diffInDays($end) + 1;

        // Limit to max 30 days for daily view
        if ($days > 30) {
            $start = $end->copy()->subDays(29);
            $days = 30;
        }

        $data = collect();
        
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            
            $dayData = Order::query()
                ->where('status', OrderStatus::PAID)
                ->whereDate('paid_at', $date)
                ->selectRaw('COALESCE(SUM(final_amount), 0) as revenue, COUNT(*) as count')
                ->first();

            $data->push([
                'label' => $date->format('d M'),
                'revenue' => (float) ($dayData->revenue ?? 0),
                'count' => (int) ($dayData->count ?? 0),
            ]);
        }

        return $data;
    }

    /**
     * Get weekly revenue data
     */
    protected function getWeeklyData(?string $startDate, ?string $endDate): Collection
    {
        $start = Carbon::parse($startDate ?? now()->subWeeks(12));
        $end = Carbon::parse($endDate ?? now());
        
        $data = collect();
        $current = $start->copy()->startOfWeek();

        while ($current <= $end) {
            $weekStart = $current->copy();
            $weekEnd = $current->copy()->endOfWeek();
            
            if ($weekEnd > $end) {
                $weekEnd = $end->copy();
            }

            $weekData = Order::query()
                ->where('status', OrderStatus::PAID)
                ->whereDate('paid_at', '>=', $weekStart)
                ->whereDate('paid_at', '<=', $weekEnd)
                ->selectRaw('COALESCE(SUM(final_amount), 0) as revenue, COUNT(*) as count')
                ->first();

            $data->push([
                'label' => 'W' . $weekStart->weekOfYear . ' ' . $weekStart->format('M'),
                'revenue' => (float) ($weekData->revenue ?? 0),
                'count' => (int) ($weekData->count ?? 0),
            ]);

            $current->addWeek();
        }

        return $data->take(12); // Limit to 12 weeks
    }

    /**
     * Get monthly revenue data
     */
    protected function getMonthlyData(?string $startDate, ?string $endDate): Collection
    {
        $start = Carbon::parse($startDate ?? now()->subMonths(11)->startOfMonth());
        $end = Carbon::parse($endDate ?? now());
        
        $data = collect();
        $current = $start->copy()->startOfMonth();

        while ($current <= $end) {
            $monthStart = $current->copy();
            $monthEnd = $current->copy()->endOfMonth();
            
            if ($monthEnd > $end) {
                $monthEnd = $end->copy();
            }

            $monthData = Order::query()
                ->where('status', OrderStatus::PAID)
                ->whereDate('paid_at', '>=', $monthStart)
                ->whereDate('paid_at', '<=', $monthEnd)
                ->selectRaw('COALESCE(SUM(final_amount), 0) as revenue, COUNT(*) as count')
                ->first();

            $data->push([
                'label' => $monthStart->translatedFormat('M Y'),
                'revenue' => (float) ($monthData->revenue ?? 0),
                'count' => (int) ($monthData->count ?? 0),
            ]);

            $current->addMonth();
        }

        return $data->take(12); // Limit to 12 months
    }

    /**
     * Get the date range based on selected period filter
     */
    protected function getDateRange(): array
    {
        $period = $this->pageFilters['period'] ?? 'last_30_days';

        return match ($period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'last_7_days' => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
            'last_30_days' => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
            'this_month' => [now()->startOfMonth(), now()->endOfDay()],
            'last_month' => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'this_year' => [now()->startOfYear(), now()->endOfDay()],
            'last_year' => [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()],
            'all_time' => [null, null],
            default => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
        };
    }

    public function getDescription(): ?string
    {
        return 'Tren pendapatan dan jumlah transaksi berdasarkan periode yang dipilih.';
    }
}
