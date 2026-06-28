<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectFeedback extends Model
{
    protected $fillable = [
        'project_id',
        'phase_id',
        'activity_id',
        'award_id',
        'type',
        'comment',
        'total_time',
        'day_difference',
        'controller_feedback',
        'total_rating',
        'qualified',
        'project_task_id',
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

    public function coins()
    {
        return $this->hasMany(EmployeeProjectCoin::class, 'feedback_id');
    }
}
