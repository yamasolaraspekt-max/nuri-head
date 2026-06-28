<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectControlPerson extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'project_task_id',
        'phase_id',
        'employee_id',
    ];

    // 🔗 Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsTo(ProjectTask::class, 'project_task_id');
    }

    public function phase()
    {
        return $this->belongsTo(TaskPhase::class, 'phase_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function activity()
    {
        return $this->belongsTo(PhaseActivity::class, 'activity_id');
    }

}
