<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeShortLeave extends Model
{
    use HasFactory;

     protected $fillable = [
        'emp_id',
        'start_date',
        'end_date', 
        'total_days',
        'total_hours',
        'year',
        'status',
        'document',
        'status_msg'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    public function calculateDuration()
    {
        return $this->end_date ? Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date)) + 1 : null;
    }
}
