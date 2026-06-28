<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeRecurringLeaveOverride extends Model
{
    protected $fillable = [
        'employee_recurring_leave_id',
        'original_date',
        'is_cancelled',
        'new_date',
        'new_all_day',
        'new_start_time',
        'new_end_time',
        'new_duration_days',
        'new_title',
        'new_description',
    ];

    protected $casts = [
        'original_date' => 'date',
        'new_date' => 'date',
        'is_cancelled' => 'boolean',
        'new_all_day' => 'boolean',
        'new_duration_days' => 'integer',
    ];

    public function leave(): BelongsTo
    {
        return $this->belongsTo(EmployeeRecurringLeave::class, 'employee_recurring_leave_id', 'id');
    }
}
