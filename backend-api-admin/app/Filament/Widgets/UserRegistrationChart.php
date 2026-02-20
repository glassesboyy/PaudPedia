<?php

namespace App\Filament\Widgets;

use App\Models\CourseEnrollment;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class UserRegistrationChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Grafik Registrasi & Enrollment';
    protected ?string $pollingInterval = '60s';
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = [
        'default' => 'full',
    ];

    protected ?string $maxHeight = '320px';
    protected string $color = 'primary';

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
        $data = $this->getChartData($startDate, $endDate);

        return [
            'datasets' => [
                [
                    'label' => 'Registrasi User',
                    'data' => $data->pluck('users')->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Enrollment Kursus',
                    'data' => $data->pluck('enrollments')->toArray(),
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $data->pluck('label')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get chart data grouped by selected filter
     */
    protected function getChartData(?string $startDate, ?string $endDate): Collection
    {
        $filter = $this->filter;

        return match ($filter) {
            'weekly' => $this->getWeeklyData($startDate, $endDate),
            'monthly' => $this->getMonthlyData($startDate, $endDate),
            default => $this->getDailyData($startDate, $endDate),
        };
    }

    /**
     * Get daily data
     */
    protected function getDailyData(?string $startDate, ?string $endDate): Collection
    {
        $start = Carbon::parse($startDate ?? now()->subDays(29));
        $end = Carbon::parse($endDate ?? now());
        $days = min($start->diffInDays($end) + 1, 30);

        if ($days > 30) {
            $start = $end->copy()->subDays(29);
        }

        $data = collect();
        
        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i);
            
            $users = User::query()
                ->whereDate('created_at', $date)
                ->count();

            $enrollments = CourseEnrollment::query()
                ->whereDate('enrolled_at', $date)
                ->count();

            $data->push([
                'label' => $date->format('d M'),
                'users' => $users,
                'enrollments' => $enrollments,
            ]);
        }

        return $data;
    }

    /**
     * Get weekly data
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

            $users = User::query()
                ->whereDate('created_at', '>=', $weekStart)
                ->whereDate('created_at', '<=', $weekEnd)
                ->count();

            $enrollments = CourseEnrollment::query()
                ->whereDate('enrolled_at', '>=', $weekStart)
                ->whereDate('enrolled_at', '<=', $weekEnd)
                ->count();

            $data->push([
                'label' => 'W' . $weekStart->weekOfYear,
                'users' => $users,
                'enrollments' => $enrollments,
            ]);

            $current->addWeek();
        }

        return $data->take(12);
    }

    /**
     * Get monthly data
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

            $users = User::query()
                ->whereDate('created_at', '>=', $monthStart)
                ->whereDate('created_at', '<=', $monthEnd)
                ->count();

            $enrollments = CourseEnrollment::query()
                ->whereDate('enrolled_at', '>=', $monthStart)
                ->whereDate('enrolled_at', '<=', $monthEnd)
                ->count();

            $data->push([
                'label' => $monthStart->translatedFormat('M Y'),
                'users' => $users,
                'enrollments' => $enrollments,
            ]);

            $current->addMonth();
        }

        return $data->take(12);
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
        return 'Tren registrasi user dan enrollment kursus.';
    }
}
