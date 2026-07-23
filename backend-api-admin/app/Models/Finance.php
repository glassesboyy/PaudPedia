<?php

namespace App\Models;

use App\Enums\FinanceType;
use App\Enums\PaymentMethod;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Finance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'type',
        'amount',
        'description',
        'month',
        'is_paid',
        'paid_at',
        'payment_method',
        'transaction_type',
        'recorded_by',
        'proof_file_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'type' => FinanceType::class,
        'is_paid' => 'boolean',
        'paid_at' => 'datetime',
        'payment_method' => PaymentMethod::class,
        'transaction_type' => TransactionType::class,
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // Indirect relationship via student
    public function school()
    {
        return $this->student->school();
    }

    // Scopes
    public function scopeByType(Builder $query, FinanceType $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSpp(Builder $query)
    {
        return $query->where('type', FinanceType::SPP);
    }

    public function scopeTabungan(Builder $query)
    {
        return $query->where('type', FinanceType::TABUNGAN);
    }

    public function scopeByMonth(Builder $query, string $month)
    {
        return $query->where('month', $month);
    }

    public function scopePaid(Builder $query)
    {
        return $query->where('is_paid', true);
    }

    public function scopeUnpaid(Builder $query)
    {
        return $query->where('is_paid', false);
    }

    // Helper Methods
    public function isSpp(): bool
    {
        return $this->type === FinanceType::SPP;
    }

    public function isTabungan(): bool
    {
        return $this->type === FinanceType::TABUNGAN;
    }

    public function isPaid(): bool
    {
        return $this->is_paid;
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}
