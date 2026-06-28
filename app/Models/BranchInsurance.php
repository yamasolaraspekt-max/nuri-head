<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchInsurance extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_expense_id',
        'branch_id',
        'expense_types_id',
        'insurance_for',
        'policy_number',
        'provider',
        'coverage_amount',
        'monthly_payable',
        'payment_cycle',
        'due_day',
        'next_due_date',
        'payment_date',
        'start_date',
        'end_date',
        'status',
        'notes',
        'document',
    ];

    protected $casts = [
        'coverage_amount' => 'decimal:2',
        'monthly_payable' => 'decimal:2',
        'due_day' => 'integer',
        'next_due_date' => 'date',
        'payment_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CANCELLED = 'cancelled';

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Aktiv',
            self::STATUS_PAUSED => 'Pausiert',
            self::STATUS_EXPIRED => 'Abgelaufen',
            self::STATUS_CANCELLED => 'Gekündigt',
        ];
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(BranchExpense::class, 'branch_expense_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function expenseType(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class, 'expense_types_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? (string) $this->status;
    }
}