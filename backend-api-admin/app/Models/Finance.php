<?php

namespace App\Models;

use App\Enums\FinanceType;
use App\Enums\PaymentMethod;
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
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'type' => FinanceType::class,
        'is_paid' => 'boolean',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    // Indirect relationship via student
    public function school()
    {
        return $this->student->school();
    }

    // Scopes
    public function scopeByType($query, FinanceType $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSpp($query)
    {
        return $query->where('type', FinanceType::SPP);
    }

    public function scopeTabungan($query)
    {
        return $query->where('type', FinanceType::TABUNGAN);
    }

    public function scopeByMonth($query, string $month)
    {
        return $query->where('month', $month);
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    public function scopeUnpaid($query)
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
