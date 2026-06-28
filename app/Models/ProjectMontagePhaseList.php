<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMontagePhaseList extends Model
{
     protected $fillable = ['project_montage_id', 'phase_id', 'activity_id'];

    public function checklist()
    {
        return $this->belongsTo(ProjectMontageChecklist::class, 'project_montage_id');
    }

    public function phase()
    {
        return $this->belongsTo(TaskPhase::class, 'phase_id');
    }
}
