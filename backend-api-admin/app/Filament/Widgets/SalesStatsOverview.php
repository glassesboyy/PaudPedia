<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;

class SalesStatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected ?string $pollingInterval = '30s';
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Statistik Penjualan';
    protected ?string $description = 'Ringkasan performa penjualan platform';

    /**
     * Only admin can view this widget
     */
    public static function canView(): bool
    {
        return Gate::allows('viewAnalytics');
    }

    protected function getStats(): array
    {
        [$startDate, $endDate] = $this->getDateRange();

        // Get current period data
        $currentPeriodOrders = Order::query()
            ->where('status', OrderStatus::PAID)
            ->when($startDate, fn (Builder $query) => $query->whereDate('paid_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('paid_at', '<=', $endDate));

        $totalRevenue = (clone $currentPeriodOrders)->sum('final_amount');
        $totalTransactions = (clone $currentPeriodOrders)->count();
        $averageOrderValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Get pending orders
        $pendingOrders = Order::query()
            ->where('status', OrderStatus::PENDING)
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->count();

        // Calculate comparison with previous period
        $periodDays = $startDate && $endDate 
            ? Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1
            : 30;

        $previousStartDate = Carbon::parse($startDate)->subDays($periodDays);
        $previousEndDate = Carbon::parse($startDate)->subDay();

        $previousPeriodRevenue = Order::query()
            ->where('status', OrderStatus::PAID)
            ->whereDate('paid_at', '>=', $previousStartDate)
            ->whereDate('paid_at', '<=', $previousEndDate)
            ->sum('final_amount');

        $previousPeriodTransactions = Order::query()
            ->where('status', OrderStatus::PAID)
            ->whereDate('paid_at', '>=', $previousStartDate)
            ->whereDate('paid_at', '<=', $previousEndDate)
            ->count();

        // Calculate growth percentages
        $revenueGrowth = $previousPeriodRevenue > 0 
            ? (($totalRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100 
            : ($totalRevenue > 0 ? 100 : 0);

        $transactionGrowth = $previousPeriodTransactions > 0 
            ? (($totalTransactions - $previousPeriodTransactions) / $previousPeriodTransactions) * 100 
            : ($totalTransactions > 0 ? 100 : 0);

        // Get chart data for sparklines (last 7 data points)
        $revenueChartData = $this->getRevenueSparklineData($startDate, $endDate);
        $transactionChartData = $this->getTransactionSparklineData($startDate, $endDate);

        return [
            Stat::make('Total Pendapatan', $this->formatCurrency($totalRevenue))
                ->description($this->formatGrowth($revenueGrowth) . ' dari periode sebelumnya')
                ->descriptionIcon($revenueGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueGrowth >= 0 ? 'success' : 'danger')
                ->chart($revenueChartData),

            Stat::make('Total Transaksi', number_format($totalTransactions))
                ->description($this->formatGrowth($transactionGrowth) . ' dari periode sebelumnya')
                ->descriptionIcon($transactionGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($transactionGrowth >= 0 ? 'success' : 'danger')
                ->chart($transactionChartData),

            Stat::make('Nilai Rata-rata Order', $this->formatCurrency($averageOrderValue))
                ->description('Average Order Value')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info'),

            Stat::make('Pesanan Pending', number_format($pendingOrders))
                ->description('Menunggu pembayaran')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 0 ? 'warning' : 'success'),
        ];
    }

    /**
     * Get revenue sparkline data
     */
    protected function getRevenueSparklineData(?string $startDate, ?string $endDate): array
    {
        $days = 7;
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::parse($endDate ?? now())->subDays($i);
            $revenue = Order::query()
                ->where('status', OrderStatus::PAID)
                ->whereDate('paid_at', $date)
                ->sum('final_amount');
            $data[] = (float) $revenue;
        }

        return $data;
    }

    /**
     * Get transaction sparkline data
     */
    protected function getTransactionSparklineData(?string $startDate, ?string $endDate): array
    {
        $days = 7;
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::parse($endDate ?? now())->subDays($i);
            $count = Order::query()
                ->where('status', OrderStatus::PAID)
                ->whereDate('paid_at', $date)
                ->count();
            $data[] = $count;
        }

        return $data;
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

    /**
     * Format currency to Indonesian Rupiah
     */
    protected function formatCurrency(float $amount): string
    {
        if ($amount >= 1000000000) {
            return 'Rp ' . number_format($amount / 1000000000, 1, ',', '.') . ' M';
        }

        if ($amount >= 1000000) {
            return 'Rp ' . number_format($amount / 1000000, 1, ',', '.') . ' Jt';
        }

        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Format growth percentage
     */
    protected function formatGrowth(float $percentage): string
    {
        $prefix = $percentage >= 0 ? '+' : '';
        return $prefix . number_format($percentage, 1) . '%';
    }
}
