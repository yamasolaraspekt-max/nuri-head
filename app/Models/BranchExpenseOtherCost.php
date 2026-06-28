<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchExpenseOtherCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_expense_id',
        'branch_id',
        'expense_type_id',
        'title',
        'category',
        'amount',
        'payment_cycle',
        'payment_date',
        'due_date',
        'vendor',
        'invoice_no',
        'status',
        'notes',
        'document',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
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

    public static function categories(): array
    {
        return [
            'electricity' => 'Strom',
            'internet' => 'Internet / Telefon',
            'cleaning' => 'Reinigung',
            'repair' => 'Reparatur',
            'tax' => 'Steuer / Gebühren',
            'office' => 'Bürobedarf',
            'other' => 'Sonstiges',
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
}
