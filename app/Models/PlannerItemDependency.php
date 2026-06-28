<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlannerItemDependency extends Model
{
    protected $table = 'planner_item_dependencies';

    protected $fillable = [
        'planner_item_id',
        'depends_on_item_id',
        'reason',
    ];

    protected $casts = [
        'planner_item_id' => 'integer',
        'depends_on_item_id' => 'integer',
    ];

    public function item()
    {
        return $this->belongsTo(PlannerItem::class, 'planner_item_id');
    }

    public function dependsOn()
    {
        return $this->belongsTo(PlannerItem::class, 'depends_on_item_id');
    }
}