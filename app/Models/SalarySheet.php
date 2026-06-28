<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalarySheet extends Model
{
    use HasFactory;
    protected $fillable = [
        'emp_id',
        'per_week',
        'per_day',
        'per_year',
        'holiday',
        'holiday_hour',
        'sick_leave',
        'sick_leave_hour',
        'health_insurance',
        'shared_wage',
        'public_holiday',
        'public_holiday_hour',
        'remaining_working_hour',
        'unproductive_working_day',
        'unproductive_working_hour',
        'productive_hour',
        'wege_per_hour',
        'monthly_salary',
        'labor_cost_hour',
        'additional_cost',
        'additional_cost_monthly',
        'additional_cost_yearly',
        'plus_additional_wage_cost',
        'gross_salary',
        'productive_hour_wege',
        'total_monthly_salary',
        'file',
        'status',
        'salary_month', 
        'salary_year'
    ];
}
