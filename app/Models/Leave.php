<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'emp_id',
        'year',
        'leave_type',
        'start_date',
        'end_date',
        'reason',
        'description',
        'paid',
        'approved',
        'leave_day',
        'duration',
        'remaining_day',
        'status',
        'created_by',
        'request_to',
        'changed_by',
        'request_back',
        'old_start',
        'old_end',
        'request_answer',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'old_start' => 'date',
        'old_end' => 'date',
        'notes' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'request_to');
    }

    public function changedBy()
    {
        return $this->belongsTo(Employee::class, 'changed_by');
    }
}