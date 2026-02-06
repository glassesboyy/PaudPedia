<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Mentor;
use App\Models\Product;
use App\Models\PromoCode;
use App\Models\School;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\Webinar;
use App\Policies\ArticlePolicy;
use App\Policies\CategoryPolicy;
use App\Policies\MentorPolicy;
use App\Policies\ProductPolicy;
use App\Policies\PromoCodePolicy;
use App\Policies\SchoolPolicy;
use App\Policies\TestimonialPolicy;
use App\Policies\UserPolicy;
use App\Policies\WebinarPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Article::class => ArticlePolicy::class,
        Category::class => CategoryPolicy::class,
        Mentor::class => MentorPolicy::class,
        Product::class => ProductPolicy::class,
        PromoCode::class => PromoCodePolicy::class,
        School::class => SchoolPolicy::class,
        Testimonial::class => TestimonialPolicy::class,
        User::class => UserPolicy::class,
        Webinar::class => WebinarPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
