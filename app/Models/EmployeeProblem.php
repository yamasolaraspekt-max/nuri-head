<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeProblem extends Model
{
    protected $table = 'employee_problem';

    protected $fillable = [
        'employee_id',
        'problem_id',
    ];

    public function problem()
    {
        return $this->belongsTo(Problem::class, 'problem_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
