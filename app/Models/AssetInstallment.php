<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetInstallment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'asset_id',
        'type',
        'branch_id',
        'installment_id',
        'purchased_from',
        'price_per_month',
        'fines',
        'installment_duration',
        'due_date',
        'total',
        'paid_by',
        'payment_method',
        'insurance_provider',
        'insurance_amount',
        'insurance_payment_month',
        'insurance_expiry_date',
        'contract_document',
        'status',
    ];

    protected $casts = [
        'price_per_month' => 'decimal:2',
        'fines' => 'decimal:2',
        'total' => 'decimal:2',
        'insurance_amount' => 'decimal:2',
        'installment_duration' => 'integer',
        'due_date' => 'date',
        'insurance_expiry_date' => 'date',
    ];

    public const STATUS_OPEN = 'open';
    public const STATUS_PAID = 'paid';
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_CANCELLED = 'cancelled';

    public static function statuses(): array
    {
        return [
            self::STATUS_OPEN => 'Offen',
            self::STATUS_PAID => 'Bezahlt',
            self::STATUS_OVERDUE => 'Überfällig',
            self::STATUS_CANCELLED => 'Storniert',
        ];
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'asset_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function paidByEmployee()
    {
        return $this->belongsTo(Employee::class, 'paid_by');
    }

    public function payments()
    {
        return $this->hasMany(InstallmentPayment::class, 'installment_id');
    }

    public function getContractUrlAttribute(): ?string
    {
        return $this->contract_document ? asset('images/installment/contract/' . $this->contract_document) : null;
    }

    public function getPaidTotalAttribute(): float
    {
        if ($this->relationLoaded('payments')) {
            return (float) $this->payments->sum('payment_amount');
        }

        return (float) $this->payments()->sum('payment_amount');
    }

    public function getRemainingTotalAttribute(): float
    {
        return max(((float) $this->total) - $this->paid_total, 0);
    }
}
