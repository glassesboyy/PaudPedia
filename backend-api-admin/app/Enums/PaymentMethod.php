<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case TRANSFER = 'transfer';
    case CREDIT_CARD = 'credit_card';
    case GOPAY = 'gopay';
    case SHOPEEPAY = 'shopeepay';
    case QRIS = 'qris';
    case BANK_TRANSFER = 'bank_transfer';
    case INDOMARET = 'indomaret';
    case ALFAMART = 'alfamart';

    /**
     * Get payment method display name
     */
    public function label(): string
    {
        return match($this) {
            self::CASH => 'Tunai',
            self::TRANSFER => 'Transfer Bank',
            self::CREDIT_CARD => 'Kartu Kredit',
            self::GOPAY => 'GoPay',
            self::SHOPEEPAY => 'ShopeePay',
            self::QRIS => 'QRIS',
            self::BANK_TRANSFER => 'Transfer Bank',
            self::INDOMARET => 'Indomaret',
            self::ALFAMART => 'Alfamart',
        };
    }

    /**
     * Get payment method icon
     */
    public function icon(): string
    {
        return match($this) {
            self::CASH => 'banknote',
            self::TRANSFER => 'building-columns',
            self::CREDIT_CARD => 'credit-card',
            self::GOPAY => 'wallet',
            self::SHOPEEPAY => 'wallet',
            self::QRIS => 'qr-code',
            self::BANK_TRANSFER => 'building-columns',
            self::INDOMARET => 'store',
            self::ALFAMART => 'store',
        };
    }

    /**
     * Get payment method color
     */
    public function color(): string
    {
        return match($this) {
            self::CASH => 'success',
            self::TRANSFER, self::BANK_TRANSFER => 'primary',
            self::CREDIT_CARD => 'warning',
            self::GOPAY => 'success',
            self::SHOPEEPAY => 'danger',
            self::QRIS => 'info',
            self::INDOMARET, self::ALFAMART => 'secondary',
        };
    }

    /**
     * Check if payment method is from Midtrans
     */
    public function isMidtrans(): bool
    {
        return in_array($this, [
            self::CREDIT_CARD,
            self::GOPAY,
            self::SHOPEEPAY,
            self::QRIS,
            self::BANK_TRANSFER,
            self::INDOMARET,
            self::ALFAMART,
        ]);
    }

    /**
     * Check if payment method is instant
     */
    public function isInstant(): bool
    {
        return in_array($this, [
            self::CASH,
            self::GOPAY,
            self::SHOPEEPAY,
            self::QRIS,
        ]);
    }

    /**
     * Check if payment method requires manual confirmation
     */
    public function requiresManualConfirmation(): bool
    {
        return in_array($this, [
            self::CASH,
            self::TRANSFER,
        ]);
    }

    /**
     * Get payment instructions
     */
    public function instructions(): string
    {
        return match($this) {
            self::CASH => 'Pembayaran tunai langsung ke sekolah',
            self::TRANSFER => 'Transfer ke rekening sekolah yang tertera',
            self::CREDIT_CARD => 'Masukkan detail kartu kredit Anda',
            self::GOPAY => 'Scan QR code atau buka aplikasi GoPay',
            self::SHOPEEPAY => 'Scan QR code atau buka aplikasi ShopeePay',
            self::QRIS => 'Scan QR code dengan aplikasi pembayaran yang mendukung QRIS',
            self::BANK_TRANSFER => 'Transfer ke virtual account yang diberikan',
            self::INDOMARET => 'Bayar di kasir Indomaret dengan kode pembayaran',
            self::ALFAMART => 'Bayar di kasir Alfamart dengan kode pembayaran',
        };
    }

    /**
     * Get all payment method names
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get all payment method values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get payment methods for school finance (cash & transfer only)
     */
    public static function schoolFinanceMethods(): array
    {
        return [
            self::CASH,
            self::TRANSFER,
        ];
    }

    /**
     * Get payment methods for e-commerce (Midtrans)
     */
    public static function ecommerceMethods(): array
    {
        return [
            self::CREDIT_CARD,
            self::GOPAY,
            self::SHOPEEPAY,
            self::QRIS,
            self::BANK_TRANSFER,
            self::INDOMARET,
            self::ALFAMART,
        ];
    }
}
