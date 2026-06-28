<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeProjectCoin extends Model
{
    protected $fillable = [
        'project_id',
        'phase_id',
        'activity_id',
        'award_id',
        'feedback_id',
        'employee_id',
        'project_task_id'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function phase()
    {
        return $this->belongsTo(TaskPhase::class);
    }

    public function activity()
    {
        return $this->belongsTo(PhaseActivity::class);
    }

    public function award()
    {
        return $this->belongsTo(ProjectAward::class);
    }

    public function feedback()
    {
        return $this->belongsTo(ProjectFeedback::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
