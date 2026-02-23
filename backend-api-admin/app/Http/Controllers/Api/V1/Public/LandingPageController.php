<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Resources\Api\V1\Public\ArticleResource;
use App\Http\Resources\Api\V1\Public\CourseResource;
use App\Http\Resources\Api\V1\Public\ProductResource;
use App\Http\Resources\Api\V1\Public\TestimonialResource;
use App\Http\Resources\Api\V1\Public\WebinarResource;
use App\Models\Article;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Product;
use App\Models\School;
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
            'title' => $settings['hero_title'] ?? $settings['site_name'] ?? 'PaudPedia',
            'subtitle' => $settings['hero_subtitle'] ?? $settings['site_tagline'] ?? null,
            'image' => $settings['hero_image'] ? asset('storage/' . $settings['hero_image']) : null,
            'cta_text' => $settings['hero_cta_text'] ?? 'Mulai Sekarang',
            'cta_link' => $settings['hero_cta_link'] ?? '/courses',
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
            'site_logo' => $settings['site_logo'] ? asset('storage/' . $settings['site_logo']) : null,
            'hero' => [
                'title' => $settings['hero_title'] ?? null,
                'subtitle' => $settings['hero_subtitle'] ?? null,
                'image' => $settings['hero_image'] ? asset('storage/' . $settings['hero_image']) : null,
                'cta_text' => $settings['hero_cta_text'] ?? 'Mulai Sekarang',
                'cta_link' => $settings['hero_cta_link'] ?? '/courses',
            ],
            'contact' => [
                'email' => $settings['contact_email'] ?? null,
                'phone' => $settings['contact_phone'] ?? null,
                'whatsapp' => $settings['contact_whatsapp'] ?? null,
                'address' => $settings['contact_address'] ?? null,
            ],
            'social_media' => array_filter([
                'instagram' => $settings['social_instagram'] ?? null,
                'facebook' => $settings['social_facebook'] ?? null,
                'youtube' => $settings['social_youtube'] ?? null,
                'linkedin' => $settings['social_linkedin'] ?? null,
                'twitter' => $settings['social_twitter'] ?? null,
                'tiktok' => $settings['social_tiktok'] ?? null,
            ]),
            'footer' => [
                'copyright' => $settings['footer_copyright'] ?? '© ' . date('Y') . ' PaudPedia. All rights reserved.',
                'description' => $settings['footer_description'] ?? null,
            ],
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
            'total_schools' => School::count(),
            'total_users' => User::count(),
            'total_courses' => Course::published()->count(),
            'total_webinars' => Webinar::active()->count(),
            'total_products' => Product::active()->count(),
            'total_articles' => Article::published()->count(),
            'total_enrollments' => CourseEnrollment::count(),
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

        return CourseResource::collection($courses)->resolve();
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
            ->upcoming()
            ->with(['mentor'])
            ->orderBy('scheduled_at', 'asc')
            ->limit(4)
            ->get();

        return WebinarResource::collection($webinars)->resolve();
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

        return ProductResource::collection($products)->resolve();
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

        return TestimonialResource::collection($testimonials)->resolve();
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

        return ArticleResource::collection($articles)->resolve();
    }
}
