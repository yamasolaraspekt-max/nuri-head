<?php

// Add these methods to app/Models/Employee.php if you want reverse relations.
// They are optional for saving, but useful for queries and debugging.

public function assignedGeneralTasks()
{
    return $this->belongsToMany(
        \App\Models\GeneralTask::class,
        'general_task_assignees',
        'employee_id',
        'general_task_id'
    )->withTimestamps();
}

public function assignedGeneralTaskSteps()
{
    return $this->belongsToMany(
        \App\Models\GeneralTaskStep::class,
        'general_task_step_assignees',
        'employee_id',
        'general_task_step_id'
    )->withTimestamps();
}

public function checkedGeneralTaskSteps()
{
    return $this->hasMany(\App\Models\GeneralTaskStep::class, 'checked_by');
}

public function createdGeneralTasks()
{
    return $this->hasMany(\App\Models\GeneralTask::class, 'created_by');
}

public function claimedGeneralTasks()
{
    return $this->hasMany(\App\Models\GeneralTask::class, 'claimed_by');
}
