<?php
 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlannerItemChecklist extends Model
{
    protected $table = 'planner_item_checklists';
    protected $guarded = [];
    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function item()
    {
        return $this->belongsTo(PlannerItem::class, 'planner_item_id');
    }
}