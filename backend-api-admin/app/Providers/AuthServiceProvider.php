<?php

namespace App\Providers;

use App\Models\Mentor;
use App\Models\Product;
use App\Models\Webinar;
use App\Policies\MentorPolicy;
use App\Policies\ProductPolicy;
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
        Mentor::class => MentorPolicy::class,
        Product::class => ProductPolicy::class,
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
