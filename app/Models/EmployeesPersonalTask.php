<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeesPersonalTask extends Model
{
    
    protected $table = 'employees_personal_tasks';
    protected $fillable = [
        'employee_id',
        'task_id',
        'status',
        'reason',  
         'note', 'change_date', 'changed_by', 'change_reason',
        'total_hour', 
    ];

    public function task()
    {
        return $this->belongsTo(PersonalTask::class, 'task_id');
    }

}
