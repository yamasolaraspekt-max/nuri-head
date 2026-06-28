<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BranchRent extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_details_id',
        'branch_id',
        'object_name',
        'object_type',
        'extra_cost',
        'rent_cost',
        'total',
        'city',
        'street',
        'house_no',
        'postcode',
        'landlord_name',
        'landlord_contact',
        'contract_start',
        'contract_end',
        'payment_cycle',
        'due_day',
        'next_due_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'extra_cost' => 'decimal:2',
        'rent_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'contract_start' => 'date',
        'contract_end' => 'date',
        'next_due_date' => 'date',
        'due_day' => 'integer',
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_TERMINATED = 'terminated';

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Aktiv',
            self::STATUS_PAUSED => 'Pausiert',
            self::STATUS_TERMINATED => 'Beendet',
        ];
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(BranchExpense::class, 'expense_details_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(BranchRentInfo::class, 'object_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? (string) $this->status;
    }
}
