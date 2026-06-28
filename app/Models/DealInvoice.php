<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DealInvoice extends Model
{
    use SoftDeletes;

    protected $table = 'deal_invoices';

    protected $fillable = [
        'deal_id',
        'created_by',
        'reviewed_by',
        'checked_by',
        'invoice_number',
        'invoice_type',
        'invoice_amount',
        'paid_amount',
        'open_amount',
        'issued_at',
        'due_date',
        'paid_at',
        'status',
        'is_storno',
        'reminder_level',
        'last_reminder_at',
    ];

    protected $casts = [
        'invoice_amount'     => 'float',
        'paid_amount'        => 'float',
        'open_amount'        => 'float',
        'issued_at'          => 'date',
        'due_date'           => 'date',
        'paid_at'            => 'date',
        'last_reminder_at'   => 'date',
        'is_storno'          => 'boolean',
        'reminder_level'     => 'integer',
    ];
 
    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(Employee::class, 'reviewed_by');
    }

    public function checker()
    {
        return $this->belongsTo(Employee::class, 'checked_by');
    }
}
