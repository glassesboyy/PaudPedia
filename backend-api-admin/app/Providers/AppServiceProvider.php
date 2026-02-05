<?php

namespace App\Providers;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Failed::class, function (Failed $event) {
            if ($event->user && ! $event->user->is_active) {
                Log::warning('inactive user login attempt', [
                    'user_id' => $event->user->id,
                    'email' => $event->user->email,
                ]);
            }
        });
    }
}
