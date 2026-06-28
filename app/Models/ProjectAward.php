<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectAward extends Model
{
    protected $fillable = [
        'project_id',
        'phase_id',
        'activity_id',
        'assigned_by',
        'coins_awarded',
        'restricted_day',
        'restricted_time',
        'reason',
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

    public function assignedBy()
    {
        return $this->belongsTo(Employee::class, 'assigned_by');
    }

    public function feedbacks()
    {
        return $this->hasMany(ProjectFeedback::class, 'award_id');
    }

    public function coins()
    {
        return $this->hasMany(EmployeeProjectCoin::class, 'award_id');
    }
}
