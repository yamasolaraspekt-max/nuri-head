<?php

 namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeMonthlyTimeBudget extends Model
{
    protected $fillable = [
        'employee_id', 'year', 'month', 'planned_minutes',
    ];

    protected $casts = [
        'year'   => 'integer',
        'month'  => 'integer',
    ];
}
