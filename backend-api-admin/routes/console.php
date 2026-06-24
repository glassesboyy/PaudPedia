<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
use App\Models\SubscriptionOrder;

Schedule::call(function () {
    SubscriptionOrder::where('status', 'pending')
        ->where('expired_at', '<', now())
        ->update(['status' => 'expired']);
})->hourly()->name('expire_subscription_orders');

use App\Models\School;

Schedule::call(function () {
    School::where('subscription_plan', 'pro')
        ->where('subscription_ended_at', '<', now())
        ->update(['subscription_plan' => 'free']);
})->daily()->name('downgrade_expired_schools');
