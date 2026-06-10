<?php

namespace App\Services\Api;

use App\Models\School;
use App\Models\SubscriptionOrder;
use App\Services\Setting\SiteSettingService;
use Midtrans\Config;
use Midtrans\Snap;

class SubscriptionPaymentService
{
    public function __construct(
        protected SiteSettingService $siteSettingService
    ) {
        $this->configureMidtrans();
    }

    /**
     * Configure Midtrans credentials from site settings.
     */
    protected function configureMidtrans(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized', true);
        Config::$is3ds = config('midtrans.is_3ds', true);
    }

    /**
     * Get the Pro Plan monthly price from site settings.
     */
    public function getProMonthlyPrice(): int
    {
        return (int) $this->siteSettingService->getSetting('pro_monthly_price', 150000);
    }

    /**
     * Create a Midtrans Snap transaction for Pro Plan upgrade.
     */
    public function createUpgradeTransaction(School $school, int $durationMonths = 1): SubscriptionOrder
    {
        $amount = $this->getProMonthlyPrice() * $durationMonths;
        $orderId = 'SUB-' . $school->id . '-' . time() . '-' . strtoupper(substr(md5(uniqid()), 0, 4));

        // Check for existing pending order and expire it
        SubscriptionOrder::where('school_id', $school->id)
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        // Create subscription order record
        $subscriptionOrder = SubscriptionOrder::create([
            'school_id' => $school->id,
            'amount' => $amount,
            'status' => 'pending',
            'midtrans_order_id' => $orderId,
            'duration_months' => $durationMonths,
            'expired_at' => now()->addHours(24),
        ]);

        // Build Midtrans payload
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'item_details' => [
                [
                    'id' => 'PRO_PLAN_' . $durationMonths . 'M',
                    'price' => $this->getProMonthlyPrice(),
                    'quantity' => $durationMonths,
                    'name' => 'PaudPedia Pro Plan (' . $durationMonths . ' Bulan)',
                ],
            ],
            'customer_details' => [
                'first_name' => $school->headmaster_name ?? $school->name,
                'email' => $school->headmaster_email ?? $school->email,
                'phone' => $school->headmaster_phone ?? $school->phone,
            ],
            'callbacks' => [
                'finish' => config('app.frontend_siakad_url', 'http://localhost:5174') . '/school/subscription?status=finish',
            ],
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit' => 'hours',
                'duration' => 24,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        $subscriptionOrder->update(['snap_token' => $snapToken]);

        return $subscriptionOrder;
    }

    /**
     * Handle successful payment — upgrade school to Pro.
     */
    public function handlePaymentSuccess(SubscriptionOrder $order, ?string $transactionId = null, ?string $paymentMethod = null): void
    {
        $order->markAsPaid($transactionId, $paymentMethod);

        $school = $order->school;
        $currentEnd = $school->subscription_ended_at;
        $startDate = ($currentEnd && $currentEnd->isFuture()) ? $currentEnd : now();

        $school->update([
            'subscription_plan' => 'pro',
            'subscription_started_at' => $school->subscription_started_at ?? now(),
            'subscription_ended_at' => $startDate->addMonths($order->duration_months),
        ]);
    }

    /**
     * Get subscription info for a school.
     */
    public function getSubscriptionInfo(School $school): array
    {
        $freeMaxStudents = (int) $this->siteSettingService->getSetting('free_max_students', 20);
        $freeMaxTeachers = (int) $this->siteSettingService->getSetting('free_max_teachers', 5);

        return [
            'plan' => $school->subscription_plan->value,
            'plan_label' => $school->subscription_plan->label(),
            'is_pro' => $school->isPro(),
            'student_usage' => $school->total_students,
            'student_limit' => $school->isPro() ? null : $freeMaxStudents,
            'teacher_usage' => $school->total_teachers,
            'teacher_limit' => $school->isPro() ? null : $freeMaxTeachers,
            'features' => $school->subscription_plan->features(),
            'subscription_started_at' => $school->subscription_started_at?->toISOString(),
            'subscription_ended_at' => $school->subscription_ended_at?->toISOString(),
            'pro_monthly_price' => $this->getProMonthlyPrice(),
            'pro_monthly_price_formatted' => 'Rp ' . number_format($this->getProMonthlyPrice(), 0, ',', '.'),
        ];
    }
}
