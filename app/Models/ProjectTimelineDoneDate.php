<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTimelineDoneDate extends Model
{
    protected $fillable = ['project_id', 'timeline_id', 'done_by', 'done_date', 'timeline_range'];

    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function timeline() {
        return $this->belongsTo(ProjectTimeline::class, 'timeline_id');
    }

    public function doneBy() {
        return $this->belongsTo(Employee::class, 'done_by');
    }
    protected $casts = [
        'done_date' => 'datetime',
    ];

 

}
