<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTimeline extends Model
{
    protected $fillable = [
        'project_id', 'phase_id', 'activity_id', 'done_by', 'edit_by',
        'start_date', 'due_date', 'is_done', 'done_range', 'done_date', 'date_difference'
    ];

    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function phase() {
        return $this->belongsTo(TaskPhase::class, 'phase_id');
    }

    public function activity() {
        return $this->belongsTo(PhaseActivities::class, 'activity_id');
    }

    public function doneBy() {
        return $this->belongsTo(Employee::class, 'done_by');
    }

    public function editBy() {
        return $this->belongsTo(Employee::class, 'edit_by');
    }

    public function doneDates() {
        return $this->hasMany(ProjectTimelineDoneDate::class, 'timeline_id');
    }

    protected $casts = [
        'start_date' => 'datetime',
        'due_date' => 'datetime',
        'done_date' => 'datetime',
    ];

    

}
