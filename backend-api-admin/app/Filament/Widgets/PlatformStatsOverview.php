<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Schools\SchoolResource;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\Webinars\WebinarResource;
use App\Models\Article;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Product;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Models\Webinar;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;

class PlatformStatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected ?string $pollingInterval = '60s';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Statistik Platform';
    protected ?string $description = 'Ringkasan data platform PaudPedia';

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

        // User statistics
        $totalUsers = User::query()->count();
        $newUsers = User::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('created_at', '<=', $endDate))
            ->count();

        // Course statistics
        $totalCourses = Course::query()->count();
        $publishedCourses = Course::query()->where('is_published', true)->count();
        $totalEnrollments = CourseEnrollment::query()->count();
        $newEnrollments = CourseEnrollment::query()
            ->when($startDate, fn (Builder $query) => $query->whereDate('enrolled_at', '>=', $startDate))
            ->when($endDate, fn (Builder $query) => $query->whereDate('enrolled_at', '<=', $endDate))
            ->count();

        // Webinar statistics
        $totalWebinars = Webinar::query()->count();
        $activeWebinars = Webinar::query()->where('is_active', true)->count();
        $upcomingWebinars = Webinar::query()
            ->where('is_active', true)
            ->where('scheduled_at', '>', now())
            ->count();

        // Product statistics
        $totalProducts = Product::query()->count();
        $activeProducts = Product::query()->where('is_active', true)->count();

        // School statistics
        $totalSchools = School::query()->count();
        $totalStudents = Student::query()->count();

        // Article statistics
        $totalArticles = Article::query()->count();
        $publishedArticles = Article::query()->where('is_published', true)->count();

        // Calculate user growth
        $periodDays = $startDate && $endDate 
            ? Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1
            : 30;

        $previousStartDate = Carbon::parse($startDate)->subDays($periodDays);
        $previousEndDate = Carbon::parse($startDate)->subDay();

        $previousNewUsers = User::query()
            ->whereDate('created_at', '>=', $previousStartDate)
            ->whereDate('created_at', '<=', $previousEndDate)
            ->count();

        $userGrowth = $previousNewUsers > 0 
            ? (($newUsers - $previousNewUsers) / $previousNewUsers) * 100 
            : ($newUsers > 0 ? 100 : 0);

        // Sparkline data
        $userSparkline = $this->getUserSparklineData($endDate);
        $enrollmentSparkline = $this->getEnrollmentSparklineData($endDate);

        return [
            Stat::make('Total Pengguna', number_format($totalUsers))
            ->descriptionIcon('heroicon-m-user-plus')
                ->description('+' . number_format($newUsers) . ' pengguna baru')
                ->color('primary')
                ->chart($userSparkline)
                ->url(UserResource::getUrl('index')),

            Stat::make('Total Kursus', number_format($totalCourses))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->description($publishedCourses . ' kursus dipublikasi')
                ->color('info')
                ->url(CourseResource::getUrl('index')),

            Stat::make('Total Enrollment', number_format($totalEnrollments))
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->description('+' . number_format($newEnrollments) . ' enrollment baru')
                ->color('success')
                ->chart($enrollmentSparkline),

            Stat::make('Total Webinar', number_format($totalWebinars))
                ->descriptionIcon('heroicon-m-video-camera')
                ->description($upcomingWebinars . ' webinar mendatang')
                ->color('warning')
                ->url(WebinarResource::getUrl('index')),

            Stat::make('Total Produk', number_format($totalProducts))
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->description($activeProducts . ' produk aktif')
                ->color('info')
                ->url(ProductResource::getUrl('index')),

            Stat::make('Total Sekolah', number_format($totalSchools))
                ->descriptionIcon('heroicon-m-building-library')
                ->description(number_format($totalStudents) . ' siswa terdaftar')
                ->color('gray')
                ->url(SchoolResource::getUrl('index')),

            Stat::make('Total Artikel', number_format($totalArticles))
                ->descriptionIcon('heroicon-m-newspaper')
                ->description($publishedArticles . ' artikel dipublikasi')
                ->color('info')
                ->url(ArticleResource::getUrl('index')),

            Stat::make('Pertumbuhan User', $this->formatGrowth($userGrowth))
                ->descriptionIcon($userGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->description('Dibandingkan periode sebelumnya')
                ->color($userGrowth >= 0 ? 'success' : 'danger'),
        ];
    }

    /**
     * Get user registration sparkline data
     */
    protected function getUserSparklineData(?string $endDate): array
    {
        $days = 7;
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::parse($endDate ?? now())->subDays($i);
            $count = User::query()
                ->whereDate('created_at', $date)
                ->count();
            $data[] = $count;
        }

        return $data;
    }

    /**
     * Get enrollment sparkline data
     */
    protected function getEnrollmentSparklineData(?string $endDate): array
    {
        $days = 7;
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::parse($endDate ?? now())->subDays($i);
            $count = CourseEnrollment::query()
                ->whereDate('enrolled_at', $date)
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
     * Format growth percentage
     */
    protected function formatGrowth(float $percentage): string
    {
        $prefix = $percentage >= 0 ? '+' : '';
        return $prefix . number_format($percentage, 1) . '%';
    }
}
