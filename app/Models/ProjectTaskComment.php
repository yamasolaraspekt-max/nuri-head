<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTaskComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'phase_id',
        'activity_id',
        'employee_id',
        'comment',
        'status',
        'parent_id',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function phase()
    {
        return $this->belongsTo(TaskPhase::class, 'phase_id');
    }

    public function activity()
    {
        return $this->belongsTo(PhaseActivity::class, 'activity_id');
    }

    public function parent()
    {
        return $this->belongsTo(ProjectTaskComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ProjectTaskComment::class, 'parent_id')->with('employee');
    }
}
