<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\Public\Landing\LandingArticleResource;
use App\Http\Resources\Api\V1\Public\Landing\LandingCourseResource;
use App\Http\Resources\Api\V1\Public\Landing\LandingProductResource;
use App\Http\Resources\Api\V1\Public\Landing\LandingTestimonialResource;
use App\Http\Resources\Api\V1\Public\Landing\LandingWebinarResource;
use App\Models\Article;
use App\Models\Course;
use App\Models\Product;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\Webinar;
use App\Services\Setting\SiteSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LandingPageController extends BaseController
{
    public function __construct(
        protected SiteSettingService $siteSettingService
    ) {}

    /**
     * Get all landing page data in a single request.
     *
     * @unauthenticated
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Cache the landing page data for 10 minutes
        $data = Cache::remember('landing_page_data', 600, function () {
            return [
                'settings' => $this->getSettings(),
                'statistics' => $this->getStatistics(),
                'featured_courses' => $this->getFeaturedCourses(),
                'featured_webinars' => $this->getFeaturedWebinars(),
                'featured_products' => $this->getFeaturedProducts(),
                'testimonials' => $this->getTestimonials(),
                'latest_articles' => $this->getLatestArticles(),
            ];
        });

        return $this->success($data, 'Data landing page berhasil dimuat');
    }

    /**
     * Get hero section data.
     *
     * @unauthenticated
     * @return JsonResponse
     */
    public function hero(): JsonResponse
    {
        $settings = $this->siteSettingService->getPublicSettings();

        $heroData = [
            'site_name' => $settings['site_name'] ?? 'PaudPedia',
            'site_tagline' => $settings['site_tagline'] ?? null,
        ];

        return $this->success($heroData, 'Data hero section berhasil dimuat');
    }

    /**
     * Get platform statistics.
     *
     * @unauthenticated
     * @return JsonResponse
     */
    public function statistics(): JsonResponse
    {
        $stats = Cache::remember('platform_statistics', 3600, function () {
            return $this->getStatistics();
        });

        return $this->success($stats, 'Statistik platform berhasil dimuat');
    }

    /**
     * Get site settings.
     *
     * @return array
     */
    protected function getSettings(): array
    {
        $settings = $this->siteSettingService->getPublicSettings();

        return [
            'site_name' => $settings['site_name'] ?? 'PaudPedia',
            'site_tagline' => $settings['site_tagline'] ?? null,
            'site_description' => $settings['site_description'] ?? null,
            'contact' => [
                'email' => $settings['contact_email'] ?? null,
                'phone' => $settings['contact_phone'] ?? null,
                'address' => $settings['contact_address'] ?? null,
            ],
            'social_media' => array_filter([
                'facebook' => $settings['social_facebook'] ?? null,
                'instagram' => $settings['social_instagram'] ?? null,
                'twitter' => $settings['social_twitter'] ?? null,
                'youtube' => $settings['social_youtube'] ?? null,
                'tiktok' => $settings['social_tiktok'] ?? null,
                'linkedin' => $settings['social_linkedin'] ?? null,
                'telegram' => $settings['social_telegram'] ?? null,
                'discord' => $settings['social_discord'] ?? null,
            ]),
        ];
    }

    /**
     * Get platform statistics.
     *
     * @return array
     */
    protected function getStatistics(): array
    {
        return [
            'total_users' => User::count(),
            'total_courses' => Course::published()->count(),
            'total_webinars' => Webinar::active()->count(),
            'total_articles' => Article::published()->count(),
        ];
    }

    /**
     * Get featured courses.
     *
     * @return array
     */
    protected function getFeaturedCourses(): array
    {
        $courses = Course::query()
            ->published()
            ->with(['mentor', 'category'])
            ->withCount('modules')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return LandingCourseResource::collection($courses)->resolve();
    }

    /**
     * Get featured webinars.
     *
     * @return array
     */
    protected function getFeaturedWebinars(): array
    {
        $webinars = Webinar::query()
            ->active()
            ->where('scheduled_at', '>', now()->subDays(30))
            ->with(['mentor'])
            ->orderBy('scheduled_at', 'asc')
            ->limit(4)
            ->get();

        return LandingWebinarResource::collection($webinars)->resolve();
    }

    /**
     * Get featured products.
     *
     * @return array
     */
    protected function getFeaturedProducts(): array
    {
        $products = Product::query()
            ->active()
            ->with(['category'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return LandingProductResource::collection($products)->resolve();
    }

    /**
     * Get featured testimonials.
     *
     * @return array
     */
    protected function getTestimonials(): array
    {
        $testimonials = Testimonial::query()
            ->approved()
            ->featured()
            ->with(['user'])
            ->orderBy('rating', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return LandingTestimonialResource::collection($testimonials)->resolve();
    }

    /**
     * Get latest articles.
     *
     * @return array
     */
    protected function getLatestArticles(): array
    {
        $articles = Article::query()
            ->published()
            ->with(['category', 'author'])
            ->recent()
            ->limit(4)
            ->get();

        return LandingArticleResource::collection($articles)->resolve();
    }
}
