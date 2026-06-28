<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BranchExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'year',
        'period_start',
        'period_end',
        'rent_total',
        'insurance_total',
        'employee_total',
        'asset_total',
        'machine_total',
        'installment_total',
        'other_total',
        'total',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'period_start' => 'date',
        'period_end' => 'date',
        'rent_total' => 'decimal:2',
        'insurance_total' => 'decimal:2',
        'employee_total' => 'decimal:2',
        'asset_total' => 'decimal:2',
        'machine_total' => 'decimal:2',
        'installment_total' => 'decimal:2',
        'other_total' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_ARCHIVED = 'archived';

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Entwurf',
            self::STATUS_ACTIVE => 'Aktiv',
            self::STATUS_CLOSED => 'Abgeschlossen',
            self::STATUS_ARCHIVED => 'Archiviert',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /** Existing table uses expense_details_id for the branch expense id. */
    public function rents(): HasMany
    {
        return $this->hasMany(BranchRent::class, 'expense_details_id');
    }

    public function rentPayments(): HasMany
    {
        return $this->hasMany(BranchRentInfo::class, 'expense_details_id');
    }

    public function insurances(): HasMany
    {
        return $this->hasMany(BranchInsurance::class, 'branch_expense_id');
    }

    public function otherCosts(): HasMany
    {
        return $this->hasMany(BranchExpenseOtherCost::class, 'branch_expense_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? (string) $this->status;
    }
}
