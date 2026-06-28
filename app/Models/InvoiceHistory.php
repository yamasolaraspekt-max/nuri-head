<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceHistory extends Model
{
    protected $fillable = [
        'invoice_id',
        'employee_id',
        'event_type',
        'old_status',
        'new_status',
        'old_values',
        'new_values',
        'note',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
