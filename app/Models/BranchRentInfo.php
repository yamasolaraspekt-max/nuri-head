<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchRentInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_details_id',
        'object_id',
        'apartment_id',
        'cold_rent',
        'extra_cost',
        'electricity_cost',
        'heating_cost',
        'repair_cost',
        'total',
        'payment_date',
        'due_date',
        'payee',
        'status',
        'notes',
    ];

    protected $casts = [
        'cold_rent' => 'decimal:2',
        'extra_cost' => 'decimal:2',
        'electricity_cost' => 'decimal:2',
        'heating_cost' => 'decimal:2',
        'repair_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'payment_date' => 'date',
        'due_date' => 'date',
    ];

    public static function statuses(): array
    {
        return [
            'open' => 'Offen',
            'paid' => 'Bezahlt',
            'overdue' => 'Überfällig',
            'cancelled' => 'Storniert',
        ];
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(BranchExpense::class, 'expense_details_id');
    }

    public function rent(): BelongsTo
    {
        return $this->belongsTo(BranchRent::class, 'object_id');
    }
}
