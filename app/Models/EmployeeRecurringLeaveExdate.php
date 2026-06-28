<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeRecurringLeaveExdate extends Model
{
    protected $table = 'employee_recurring_leave_exdates';

    public $timestamps = false;

    protected $fillable = [
        'leave_id',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function leave(): BelongsTo
    {
        return $this->belongsTo(EmployeeRecurringLeave::class, 'leave_id', 'id');
    }
}
