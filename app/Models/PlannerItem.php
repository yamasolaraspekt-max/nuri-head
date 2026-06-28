<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlannerItem extends Model
{
    use SoftDeletes;

    protected $table = 'planner_items';

    protected $fillable = [
        'plan_id','source_type','source_id','title','category','description',
        'duration_minutes','status','planned_start_at','planned_end_at','sort_order','meta','client_uid',
        'started_at','paused_at','stopped_at','pause_reason','last_status_changed_at',
    ];

    protected $casts = [
        'planned_start_at' => 'datetime',
        'planned_end_at'   => 'datetime',
        'meta'             => 'array',
        'started_at'              => 'datetime',
        'paused_at'               => 'datetime',
        'stopped_at'              => 'datetime',
        'last_status_changed_at'  => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(PlannerPlan::class, 'plan_id');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'planner_item_employees', 'planner_item_id', 'employee_id')
            ->withPivot(['role'])
            ->withTimestamps();
    }

    public function assets()
    {
        return $this->belongsToMany(Asset::class, 'planner_item_assets', 'planner_item_id', 'asset_id')
            ->withPivot(['qty','notes'])
            ->withTimestamps();
    }

   public function dependencies()
    {
        // Items that THIS item depends on (Prerequisites)
        return $this->belongsToMany(PlannerItem::class, 'planner_item_dependencies', 'planner_item_id', 'depends_on_item_id')
            ->withPivot('reason')
            ->withTimestamps();
    }

    public function dependents()
    {
        // Items that depend on THIS item (Successors)
        return $this->belongsToMany(PlannerItem::class, 'planner_item_dependencies', 'depends_on_item_id', 'planner_item_id')
            ->withPivot('reason')
            ->withTimestamps();
    }

    public function checklists()
    {
        return $this->hasMany(PlannerItemChecklist::class, 'planner_item_id')->orderBy('sort_order');
    }


    public function masterSets()
    {
        return $this->belongsToMany(\App\Models\MasterSet::class, 'planner_item_master_sets')
            ->withTimestamps();
    }

    public function materials()
    {
        return $this->hasMany(PlannerItemMaterial::class);
    }
}
