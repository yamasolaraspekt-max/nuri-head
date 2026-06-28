<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalTaskHistory extends Model
{
    protected $table = 'personal_task_histories';

    protected $fillable = [
        'task_id',
        'employee_id',
        'type',
        'title',
        'description',
    ];

    public function task()
    {
        return $this->belongsTo(PersonalTask::class, 'task_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
