<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterSet extends Model
{
    use SoftDeletes;

   protected $fillable = [
        'article_group_id',
        'name',
        'description',
        'responsible_department_position_id',
        'status',
        // New Fields
        'creator_id',
        'count_copy',
        'count_offer',
        'is_locked',
        'main_total',
        'sub_total',
        'labor_total',
        'total',
        'costing_set_id',
        'costing_rate_mode',
        'costing_fallback',
    ];


     public function costingSet()
        {
            return $this->belongsTo(\App\Models\CostingSet::class, 'costing_set_id');
        }

    // New Relation: The employee who created this set
    public function creator()
    {
        return $this->belongsTo(Employee::class, 'creator_id');
    }

    public function articleGroup()
    {
        return $this->belongsTo(ArticleGroup::class, 'article_group_id');
    }

    public function responsibleDepartmentPosition()
    {
        return $this->belongsTo(DepartmentPosition::class, 'responsible_department_position_id');
    }

    public function components()
    {
        return $this->hasMany(MasterSetComponent::class, 'master_set_id');
    }

    public function labor()
    {
        return $this->hasMany(MasterSetLabor::class, 'master_set_id');
    }

 
     public function taskPhase()
    {
        return $this->belongsTo(TaskPhase::class, 'task_phase_id');
    }

    // 🔗 New Relation: Belongs to a specific Activity
    public function phaseActivity()
    {
        return $this->belongsTo(PhaseActivities::class, 'phase_activity_id');
    }

    public function plannerItems()
    {
        return $this->belongsToMany(\App\Models\PlannerItem::class, 'planner_item_master_sets')
            ->withTimestamps();
    }

    public function tasks()
    {
        return $this->hasMany(\App\Models\MasterSetTask::class, 'master_set_id')->orderBy('sort_order');
    }

    public function checklistLinks()
    {
        return $this->hasMany(\App\Models\MasterSetChecklist::class, 'master_set_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function checklists()
    {
        // optional convenience (not required)
        return $this->belongsToMany(
            \App\Models\MaintenanceChecklist::class,
            'master_set_checklists',
            'master_set_id',
            'maintenance_checklist_id'
        )->withPivot([
            'trigger','is_required','sort_order',
            'checklist_title_snapshot','checklist_type_snapshot'
        ])->withTimestamps();
    }

    // app/Models/MasterSet.php
    public function groups()
    {
    return $this->belongsToMany(\App\Models\MasterSetGroup::class, 'master_set_group_master_set')
        ->withTimestamps();
    }

    public function cartTargets()
    {
        return $this->hasMany(\App\Models\MasterSetCart::class, 'target_master_set_id');
    }

}
