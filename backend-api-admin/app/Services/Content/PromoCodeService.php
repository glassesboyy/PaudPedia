<?php

namespace App\Services\Content;

use App\Models\PromoCode;
use Illuminate\Support\Facades\DB;

class PromoCodeService
{
    /**
     * Toggle active status of promo code
     *
     * @param PromoCode $promoCode
     * @return PromoCode
     * @throws \Exception
     */
    public function toggleActiveStatus(PromoCode $promoCode): PromoCode
    {
        DB::beginTransaction();
        try {
            $promoCode->is_active = !$promoCode->is_active;
            $promoCode->save();

            DB::commit();

            return $promoCode->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Check if promo code can be deleted
     *
     * @param PromoCode $promoCode
     * @return bool
     */
    public function canBeDeleted(PromoCode $promoCode): bool
    {
        // Cannot delete if promo code has been used in orders
        return $promoCode->orders()->count() === 0;
    }

    /**
     * Validate promo code for given amount
     *
     * @param string $code
     * @param float $amount
     * @return array{valid: bool, promo_code: PromoCode|null, message: string|null}
     */
    public function validatePromoCode(string $code, float $amount): array
    {
        $promoCode = PromoCode::where('code', strtoupper($code))->first();

        if (!$promoCode) {
            return [
                'valid' => false,
                'promo_code' => null,
                'message' => 'Kode promo tidak ditemukan',
            ];
        }

        if (!$promoCode->is_active) {
            return [
                'valid' => false,
                'promo_code' => $promoCode,
                'message' => 'Kode promo tidak aktif',
            ];
        }

        if ($promoCode->start_date && $promoCode->start_date->isFuture()) {
            return [
                'valid' => false,
                'promo_code' => $promoCode,
                'message' => 'Kode promo belum dimulai',
            ];
        }

        if ($promoCode->end_date && $promoCode->end_date->isPast()) {
            return [
                'valid' => false,
                'promo_code' => $promoCode,
                'message' => 'Kode promo sudah kadaluarsa',
            ];
        }

        if ($promoCode->usage_limit && $promoCode->usage_count >= $promoCode->usage_limit) {
            return [
                'valid' => false,
                'promo_code' => $promoCode,
                'message' => 'Kuota penggunaan kode promo sudah habis',
            ];
        }

        if ($promoCode->min_purchase_amount && $amount < $promoCode->min_purchase_amount) {
            return [
                'valid' => false,
                'promo_code' => $promoCode,
                'message' => 'Minimal pembelian Rp ' . number_format($promoCode->min_purchase_amount, 0, ',', '.'),
            ];
        }

        return [
            'valid' => true,
            'promo_code' => $promoCode,
            'message' => null,
        ];
    }

    /**
     * Calculate discount amount
     *
     * @param PromoCode $promoCode
     * @param float $originalAmount
     * @return float
     */
    public function calculateDiscount(PromoCode $promoCode, float $originalAmount): float
    {
        return $promoCode->discount_type->calculateDiscount(
            $promoCode->discount_value,
            $originalAmount,
            $promoCode->max_discount_amount
        );
    }

    /**
     * Increment usage count when promo code is used
     *
     * @param PromoCode $promoCode
     * @return PromoCode
     * @throws \Exception
     */
    public function incrementUsageCount(PromoCode $promoCode): PromoCode
    {
        DB::beginTransaction();
        try {
            $promoCode->increment('usage_count');

            DB::commit();

            return $promoCode->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
